<?php

if(!defined('ABSPATH')){exit;}

/** system pages */
function custom_system_auth_pages_callback() {

    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));
    $general_fields = get_fields('options');

    if (
        $path_segments[0] == $general_fields['auth']['login']['url'] ||
        $path_segments[0] == $general_fields['auth']['sign_up']['url'] ||
        $path_segments[0] == $general_fields['auth']['forgot_password']['url'] ||
        $path_segments[0] == $general_fields['profile']['url']
    ) {

        if(is_user_logged_in() && $path_segments[0] != $general_fields['profile']['url']){
            wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
            exit;
        }

        if(!is_user_logged_in() && $path_segments[0] == $general_fields['profile']['url']){
            wp_redirect( BLOGINFO_URL );
            exit;
        }

        if (empty($_GET) && substr($_SERVER['REQUEST_URI'], -1) !== '/') {
            wp_redirect(BLOGINFO_URL . '/' . trim($_SERVER['REQUEST_URI'], '/') . '/');
            exit();
        }

        add_action('wp', function(){ status_header( 200 ); });

        $context = Timber::context();

        if (
            $path_segments[0] == $general_fields['auth']['login']['url'] ||
            $path_segments[0] == $general_fields['auth']['sign_up']['url'] ||
            $path_segments[0] == $general_fields['auth']['forgot_password']['url']
        ){
            $context['links'] = array(
                array(
                    'url' => BLOGINFO_URL . '/' . $general_fields['auth']['login']['url'] . '/',
                    'title' => __('Login', TEXTDOMAIN)
                ),
                array(
                    'url' => BLOGINFO_URL . '/' . $general_fields['auth']['sign_up']['url'] . '/',
                    'title' => __('Sign Up', TEXTDOMAIN)
                ),
                array(
                    'url' => BLOGINFO_URL . '/' . $general_fields['auth']['forgot_password']['url'] . '/',
                    'title' => __('Forgot Password', TEXTDOMAIN)
                ),
                array(
                    'url' => BLOGINFO_URL,
                    'title' => __('Home page', TEXTDOMAIN)
                )
            );
        }

        if($path_segments[0] == $general_fields['auth']['login']['url']) {

            $template = 'auth/login.twig';
            $title = __('Login', TEXTDOMAIN);
            $text = $general_fields['auth']['login']['text'];
            $context['links'] = array_values(array_filter($context['links'], fn($subArray) => $subArray['title'] !== $title));

            if (isset($_POST['uEmail']) && isset($_POST['uPassword']) && isset($_POST['nonce'])) {

                $nonce = sanitize_text_field($_POST['nonce']);

                if (wp_verify_nonce($nonce, 'login')) {

                    $uEmail_trim = trim($_POST['uEmail']);
                    $uEmail_secure = wp_unslash(htmlspecialchars($uEmail_trim, ENT_QUOTES, 'UTF-8'));

                    if(filter_var($uEmail_secure, FILTER_VALIDATE_EMAIL) && strlen($uEmail_secure) < 60){

                        $user = get_user_by('email', $uEmail_secure);

                        if($user){

                            $password = wp_unslash($_POST['uPassword']);

                            if(strlen($password) < 60){

                                if($user->ID && wp_check_password( $password, $user->data->user_pass )){

                                    wp_set_current_user( $user->ID, $user->user_login );
                                    wp_set_auth_cookie( $user->ID, true );
                                    do_action( 'wp_login', $user->user_login );

                                    if($general_fields['emails']['send_authorization_security_letters']){
                                        $search = array(
                                            '[session]'
                                        );
                                        $replace = array(
                                            get_session_info($_SERVER['REMOTE_ADDR'])
                                        );

                                        $content = Timber::compile( 'email/email.twig', array(
                                            'TEXTDOMAIN' => TEXTDOMAIN,
                                            'BLOGINFO_NAME' => BLOGINFO_NAME,
                                            'BLOGINFO_URL' => BLOGINFO_URL,
                                            'subject' => $general_fields['emails']['auth']['login_subject'],
                                            'text' => str_replace($search, $replace, $general_fields['emails']['auth']['login_text'])
                                        ));
                                        send_email($uEmail_secure, $general_fields['emails']['auth']['login_subject'], $content);
                                    }

                                    wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
                                    exit;

                                } else {
                                    $context['notify'] = add_notify('error', __('Email address/password do not match.', TEXTDOMAIN), true);
                                }

                            } else {
                                $context['notify'] = add_notify('error', __('Password is not valid!', TEXTDOMAIN), true);
                            }

                        } else {
                            $context['notify'] = add_notify('error', __('Email address/password do not match.', TEXTDOMAIN), true);
                        }

                    } else {
                        wp_redirect( BLOGINFO_URL );
                        exit;
                    }

                } else {
                    wp_redirect( BLOGINFO_URL );
                    exit;
                }

            }

        } elseif ($path_segments[0] == $general_fields['auth']['sign_up']['url']){

            $template = 'auth/sign-up.twig';
            $title = __('Sign Up', TEXTDOMAIN);
            $text = $general_fields['auth']['sign_up']['text'];
            $context['links'] = array_values(array_filter($context['links'], fn($subArray) => $subArray['title'] !== $title));

            if (isset($_POST['uEmail']) && isset($_POST['uPassword']) && isset($_POST['nonce'])) {

                $nonce = sanitize_text_field($_POST['nonce']);

                if (wp_verify_nonce($nonce, 'sign-up')) {

                    $uEmail_secure = htmlspecialchars(trim($_POST['uEmail']), ENT_QUOTES, 'UTF-8');

                    if(filter_var($uEmail_secure, FILTER_VALIDATE_EMAIL) && strlen($uEmail_secure) < 60){

                        $user = get_user_by('email', $uEmail_secure);

                        if(!$user){

                            $password = wp_unslash($_POST['uPassword']);

                            if(strlen($password) < 60){

                                $password_strength = check_password_strength($password);

                                if($password_strength == 'ok'){

                                    $user_data_arr = array(
                                        'user_login'			=> $uEmail_secure,
                                        'user_pass'				=> $password,
                                        'user_email'			=> $uEmail_secure,
                                        'role'					=> 'client'
                                    );

                                    $new_user_id = wp_insert_user( $user_data_arr );

                                    if($new_user_id){

                                        $user_email_verification_code_for_link = random_int(1000000000, 9999999999);
                                        $user_email_verification_code = random_int(1000, 9999);

                                        update_user_meta( $new_user_id, 'user_email_verification_code_for_link', $user_email_verification_code_for_link );
                                        update_user_meta( $new_user_id, 'user_email_verification_code', $user_email_verification_code );
                                        update_user_meta( $new_user_id, 'nickname', $uEmail_secure );

                                        $arr_for_link = array(
                                            'action' => 'verify_email',
                                            'user_id' => $new_user_id,
                                            'verification_code' => $user_email_verification_code_for_link,
                                        );
                                        $json_for_link = json_encode($arr_for_link);
                                        $encrypted_for_link = custom_encrypt_decrypt('encrypt', $json_for_link);

                                        $search = array(
                                            '[button]',
                                            '[code]',
                                            '[session]'
                                        );
                                        $replace = array(
                                            get_email_part('button', array(
                                                'link' => DO_URL . $encrypted_for_link,
                                                'title' => __('Confirm my Email', TEXTDOMAIN)
                                            )),
                                            emoji_numbers($user_email_verification_code),
                                            get_session_info($_SERVER['REMOTE_ADDR'])
                                        );

                                        $content = Timber::compile( 'email/email.twig', array(
                                            'TEXTDOMAIN' => TEXTDOMAIN,
                                            'BLOGINFO_NAME' => BLOGINFO_NAME,
                                            'BLOGINFO_URL' => BLOGINFO_URL,
                                            'subject' => $general_fields['emails']['auth']['sign_up_subject'],
                                            'text' => str_replace($search, $replace, $general_fields['emails']['auth']['sign_up_text'])
                                        ));
                                        send_email($uEmail_secure, $general_fields['emails']['auth']['sign_up_subject'], $content);

                                        $new_user = get_user_by('id', $new_user_id);
                                        wp_set_current_user( $new_user->ID, $new_user->user_login );
                                        wp_set_auth_cookie( $new_user->ID, true );
                                        do_action( 'wp_login', $new_user->user_login );

                                        add_notify('success', __('Congratulations! Your new account has been created! Check your Email for confirmation.', TEXTDOMAIN));

                                        wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
                                        exit;

                                    } else {

                                        wp_redirect( BLOGINFO_URL );
                                        exit;

                                    }

                                } else {
                                    $context['notify'] = add_notify('error', $password_strength, true);
                                }

                            } else {
                                $context['notify'] = add_notify('error', __('Password is not valid!', TEXTDOMAIN), true);
                            }

                        } else {
                            $context['notify'] = add_notify('error', __('This email address is already taken!', TEXTDOMAIN), true);
                        }

                    } else {

                        wp_redirect( BLOGINFO_URL );
                        exit;

                    }

                } else {

                    wp_redirect( BLOGINFO_URL );
                    exit;

                }

            }

        } elseif ($path_segments[0] == $general_fields['auth']['forgot_password']['url']){

            $template = 'auth/forgot-password.twig';
            $title = __('Forgot Password', TEXTDOMAIN);
            $text = $general_fields['auth']['forgot_password']['text'];
            $context['links'] = array_values(array_filter($context['links'], fn($subArray) => $subArray['title'] !== $title));

            if (isset($_POST['uEmail']) && isset($_POST['nonce'])) {

                $nonce = sanitize_text_field($_POST['nonce']);

                if (wp_verify_nonce($nonce, 'forgot-password')) {

                    $uEmail_secure = htmlspecialchars(trim($_POST['uEmail']), ENT_QUOTES, 'UTF-8');

                    if(filter_var($uEmail_secure, FILTER_VALIDATE_EMAIL) && strlen($uEmail_secure) < 60){

                        $user = get_user_by('email', $uEmail_secure);

                        if($user){

                            $password_recovery_code_for_link = random_int(1000000000, 9999999999);

                            update_user_meta($user->ID, 'password_recovery_code_for_link', $password_recovery_code_for_link);

                            $reset_request_nonce = wp_create_nonce('reset-password-request');

                            $arr_for_link = array(
                                'action' => 'password_recovery_request',
                                'user_id' => $user->ID,
                                'password_recovery_code_for_link' => $password_recovery_code_for_link,
                                'nonce' => $reset_request_nonce
                            );
                            $json_for_link = json_encode($arr_for_link);
                            $encrypted_for_link = custom_encrypt_decrypt('encrypt', $json_for_link);

                            $search = array(
                                '[button]',
                                '[session]'
                            );
                            $replace = array(
                                get_email_part('button', array(
                                    'link' => DO_URL . $encrypted_for_link,
                                    'title' => __('Change my password', TEXTDOMAIN)
                                )),
                                get_session_info($_SERVER['REMOTE_ADDR'])
                            );

                            $content = Timber::compile( 'email/email.twig', array(
                                'TEXTDOMAIN' => TEXTDOMAIN,
                                'BLOGINFO_NAME' => BLOGINFO_NAME,
                                'BLOGINFO_URL' => BLOGINFO_URL,
                                'subject' => $general_fields['emails']['auth']['reset_password_request_subject'],
                                'text' => str_replace($search, $replace, $general_fields['emails']['auth']['reset_password_request_text'])
                            ));
                            send_email($uEmail_secure, $general_fields['emails']['auth']['reset_password_request_subject'], $content);

                            $context['notify'] = add_notify('success', __('Verification code sent', TEXTDOMAIN), true);
                        } else {
                            $context['notify'] = add_notify('error', __('No account found for this email', TEXTDOMAIN), true);
                        }
                    } else {
                        wp_redirect( BLOGINFO_URL );
                        exit;
                    }
                } else {
                    wp_redirect( BLOGINFO_URL );
                    exit;
                }

            }

        } elseif ($path_segments[0] == $general_fields['profile']['url']){

            if(!isset($path_segments[1])){

                $template = 'profile/orders.twig';
                $title = __('Orders', TEXTDOMAIN);
                $context['current_page'] = 'orders';

            } elseif ( $path_segments[1] == 'edit'){

                if (isset($_POST['nonce']) && isset($_GET['profile']) && $_GET['profile'] == 'update') {
                    $nonce = sanitize_text_field($_POST['nonce']);
                    if (wp_verify_nonce($nonce, 'profile-update')) {
                        $user_id = get_current_user_id();

                        if (isset($_POST['f_name']) && $_POST['f_name']) {
                            update_user_meta( $user_id, 'first_name', htmlspecialchars($_POST['f_name'], ENT_QUOTES, 'UTF-8') );
                        } else {
                            update_user_meta( $user_id, 'first_name', false );
                        }

                        if (isset($_POST['l_name']) && $_POST['l_name']) {
                            update_user_meta( $user_id, 'last_name', htmlspecialchars($_POST['l_name'], ENT_QUOTES, 'UTF-8') );
                        } else {
                            update_user_meta( $user_id, 'last_name', false );
                        }

                        if (isset($_POST['user_phone']) && $_POST['user_phone']) {
                            if(check_phone(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8'))){
                                $user_phone = get_user_meta($user_id, 'user_phone', true);
                                if($user_phone != fix_phone_format(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8'))){
                                    $uhp = get_users(array(
                                        'meta_key' => 'user_phone',
                                        'meta_value' => htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8')
                                    ));
                                    if(empty($uhp)){
                                        update_user_meta( $user_id, 'user_phone_confirmed', false );
                                        update_user_meta( $user_id, 'user_phone', fix_phone_format(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8')));
                                        $user_sms_verification_code = random_int(1000, 9999);
                                        update_user_meta( $user_id, 'user_sms_verification_code', $user_sms_verification_code );
                                        $sms_message = __("Your verification code:", TEXTDOMAIN) . ' ' . emoji_numbers($user_sms_verification_code);
                                        send_sms(fix_phone_format(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8')), $sms_message);
                                    } else {
                                        $context['notify'][] = add_notify('warning', __('This phone number is already taken', TEXTDOMAIN), true);
                                    }
                                }
                            } else {
                                $context['notify'][] = add_notify('warning', __('Enter valid phone number', TEXTDOMAIN), true);
                            }
                        } else {
                            update_user_meta( $user_id, 'user_phone', false );
                            update_user_meta( $user_id, 'user_phone_confirmed', false );
                        }

                        $user = new Timber\User($user_id);
                        $context['user'] = $user;
                        $context['notify'][] = add_notify('success', __('Profile updated', TEXTDOMAIN), true);
                    }
                }

                $template = 'profile/edit.twig';
                $title = __('Edit profile', TEXTDOMAIN);
                $context['current_page'] = 'edit';

            } elseif ( $path_segments[1] == 'change-password'){

                if (isset($_POST['nonce']) && isset($_GET['password']) && $_GET['password'] == 'change' && isset($_POST['u_o_password']) && $_POST['u_o_password'] && isset($_POST['u_n_password']) && $_POST['u_n_password']) {
                    $nonce = sanitize_text_field($_POST['nonce']);
                    if (wp_verify_nonce($nonce, 'password-change')) {
                        $user_id = get_current_user_id();
                        $user = get_userdata($user_id);
                        $o_password = wp_unslash($_POST['u_o_password']);
                        $n_password = wp_unslash($_POST['u_n_password']);
                        if(strlen($o_password) < 60){
                            if($user_id && wp_check_password( $o_password, $user->data->user_pass )){
                                $password_strength = check_password_strength($n_password);
                                if($password_strength == 'ok'){
                                    if($n_password != $o_password){

                                        $search = array(
                                            '[session]'
                                        );
                                        $replace = array(
                                            get_session_info($_SERVER['REMOTE_ADDR'])
                                        );

                                        $content = Timber::compile( 'email/email.twig', array(
                                            'TEXTDOMAIN' => TEXTDOMAIN,
                                            'BLOGINFO_NAME' => BLOGINFO_NAME,
                                            'BLOGINFO_URL' => BLOGINFO_URL,
                                            'subject' => $general_fields['emails']['auth']['password_change_subject'],
                                            'text' => str_replace($search, $replace, $general_fields['emails']['auth']['password_change_text'])
                                        ));
                                        send_email($user->user_email, $general_fields['emails']['auth']['password_change_subject'], $content);

                                        wp_set_password( $n_password, $user_id );
                                        wp_set_current_user( $user_id, $user->user_login );
                                        wp_set_auth_cookie( $user->ID, true );
                                        do_action( 'wp_login', $user->user_login );
                                        $context['notify'][] = add_notify('success', __('The password has been changed', TEXTDOMAIN), true);
                                    } else {
                                        $context['notify'][] = add_notify('warning', __('The password has not been changed', TEXTDOMAIN), true);
                                    }
                                } else {
                                    $context['notify'] = add_notify('error', $password_strength, true);
                                }
                            } else {
                                $context['notify'][] = add_notify('error', __('Wrong password', TEXTDOMAIN), true);
                            }
                        }
                    }
                }

                $template = 'profile/change-password.twig';
                $title = __('Change password', TEXTDOMAIN);
                $context['current_page'] = 'change-password';

            } elseif ( $path_segments[1] == 'change-email'){

                if (isset($_POST['nonce']) && isset($_GET['email']) && $_GET['email'] == 'change' && isset($_POST['u_email']) && $_POST['u_email']) {
                    $nonce = sanitize_text_field($_POST['nonce']);
                    if (wp_verify_nonce($nonce, 'email-change')) {
                        $user_id = get_current_user_id();
                        $user = get_userdata($user_id);
                        $password = wp_unslash($_POST['u_password']);

                        if(strlen($password) < 60){

                            if($user_id && wp_check_password( $password, $user->data->user_pass )){
                                $user_email = $user->user_email;
                                $new_u_email_trim = trim($_POST['u_email']);
                                $new_u_email_secure = wp_unslash(htmlspecialchars($new_u_email_trim, ENT_QUOTES, 'UTF-8'));
                                if(filter_var($new_u_email_secure, FILTER_VALIDATE_EMAIL) && strlen($new_u_email_secure) < 60){
                                    if($new_u_email_secure != $user_email){
                                        $args = array(
                                            'ID'         => $user_id,
                                            'user_email' => $new_u_email_secure
                                        );
                                        wp_update_user($args);
                                        update_user_meta( $user_id, 'user_email_confirmed', false );
                                        $context['notify'][] = add_notify('success', __('Email successfully changed', TEXTDOMAIN), true);

                                        $user_email_verification_code_for_link = random_int(1000000000, 9999999999);
                                        $user_email_verification_code = random_int(1000, 9999);

                                        update_user_meta( $user_id, 'user_email_verification_code_for_link', $user_email_verification_code_for_link );
                                        update_user_meta( $user_id, 'user_email_verification_code', $user_email_verification_code );
                                        update_user_meta( $user_id, 'nickname', $new_u_email_secure );

                                        $arr_for_link = array(
                                            'action' => 'verify_email',
                                            'user_id' => $user_id,
                                            'verification_code' => $user_email_verification_code_for_link,
                                        );
                                        $json_for_link = json_encode($arr_for_link);
                                        $encrypted_for_link = custom_encrypt_decrypt('encrypt', $json_for_link);

                                        $search = array(
                                            '[button]',
                                            '[code]',
                                            '[session]'
                                        );
                                        $replace = array(
                                            get_email_part('button', array(
                                                'link' => DO_URL . $encrypted_for_link,
                                                'title' => __('Confirm my Email', TEXTDOMAIN)
                                            )),
                                            emoji_numbers($user_email_verification_code),
                                            get_session_info($_SERVER['REMOTE_ADDR'])
                                        );

                                        $content = Timber::compile( 'email/email.twig', array(
                                            'TEXTDOMAIN' => TEXTDOMAIN,
                                            'BLOGINFO_NAME' => BLOGINFO_NAME,
                                            'BLOGINFO_URL' => BLOGINFO_URL,
                                            'subject' => $general_fields['emails']['auth']['sign_up_subject'],
                                            'text' => str_replace($search, $replace, $general_fields['emails']['auth']['sign_up_text'])
                                        ));
                                        send_email($new_u_email_secure, $general_fields['emails']['auth']['sign_up_subject'], $content);

                                    } else {
                                        $context['notify'][] = add_notify('warning', __('Email not changed', TEXTDOMAIN), true);
                                    }
                                }
                            } else {
                                $context['notify'][] = add_notify('error', __('Wrong password', TEXTDOMAIN), true);
                            }
                        }


                        $user = new Timber\User($user_id);
                        $context['user'] = $user;
                    }
                }

                $template = 'profile/change-email.twig';
                $title = __('Change email', TEXTDOMAIN);
                $context['current_page'] = 'change-email';

            } elseif ($path_segments[1]){
                wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
                exit;
            }

        }

        $context['title'] = $title;
        $context['text'] = $text;
        Timber::render( $template, $context );
        exit;
    }

}
add_action( 'init', 'custom_system_auth_pages_callback' );

/** system pages titles */
add_filter( 'document_title_parts', function( $title_parts_array ) {
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));
    $general_fields = get_fields('options');
    if($path_segments[0] == $general_fields['auth']['login']['url']){
        $title_parts_array['title'] = __('Login', TEXTDOMAIN);
    } elseif($path_segments[0] == $general_fields['auth']['sign_up']['url']){
        $title_parts_array['title'] = __('Sign Up', TEXTDOMAIN);
    } elseif($path_segments[0] == $general_fields['auth']['forgot_password']['url']){
        $title_parts_array['title'] = __('Forgot Password', TEXTDOMAIN);
    } elseif($path_segments[0] == $general_fields['profile']['url']){
        if(!isset($path_segments[1])){
            $title_parts_array['title'] = __('Orders', TEXTDOMAIN);
        } elseif ( $path_segments[1] == 'edit'){
            $title_parts_array['title'] = __('Edit profile', TEXTDOMAIN);
        } elseif ( $path_segments[1] == 'change-email'){
            $title_parts_array['title'] = __('Change email', TEXTDOMAIN);
        } elseif ( $path_segments[1] == 'change-password'){
            $title_parts_array['title'] = __('Change password', TEXTDOMAIN);
        }
    }
    return $title_parts_array;
});
