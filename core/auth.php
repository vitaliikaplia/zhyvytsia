<?php

if(!defined('ABSPATH')){exit;}

/** Custom auth pages */
function custom_system_auth_pages_callback() {

    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));
    $options = get_field('auth', 'options');

    if ($path_segments[0] == $options['login']['url'] || $path_segments[0] == $options['sign_up']['url'] || $path_segments[0] == $options['forgot_password']['url']) {

        if(is_user_logged_in()){
            wp_redirect( BLOGINFO_URL );
            exit;
        }

        if (substr($_SERVER['REQUEST_URI'], -1) !== '/') {
            wp_redirect(BLOGINFO_URL . '/' . str_replace('/','',stripslashes($_SERVER['REQUEST_URI'])) . '/');
            exit();
        }

        add_action('wp', function(){ status_header( 200 ); });

        $context = Timber::context();
        $context['links'] = array(
            array(
                'url' => BLOGINFO_URL . '/' . $options['login']['url'] . '/',
                'title' => __('Login', TEXTDOMAIN)
            ),
            array(
                'url' => BLOGINFO_URL . '/' . $options['sign_up']['url'] . '/',
                'title' => __('Sign Up', TEXTDOMAIN)
            ),
            array(
                'url' => BLOGINFO_URL . '/' . $options['forgot_password']['url'] . '/',
                'title' => __('Forgot Password', TEXTDOMAIN)
            ),
            array(
                'url' => BLOGINFO_URL,
                'title' => __('Home page', TEXTDOMAIN)
            )
        );

        if($path_segments[0] == $options['login']['url']){

            $template = 'login.twig';
            $title = __('Login', TEXTDOMAIN);
            $text = $options['login']['text'];
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

                                    $emails = get_field('emails', 'options');

                                    if($emails['send_authorization_security_letters']){
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
                                            'subject' => $emails['auth']['login_subject'],
                                            'text' => str_replace($search, $replace, $emails['auth']['login_text'])
                                        ));
                                        send_email($uEmail_secure, $emails['auth']['login_subject'], $content);
                                    }

                                    wp_redirect( get_the_permalink(get_option('profile_page')) );
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

        } elseif ($path_segments[0] == $options['sign_up']['url']){

            $template = 'sign-up.twig';
            $title = __('Sign Up', TEXTDOMAIN);
            $text = $options['sign_up']['text'];
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

                                        update_user_meta( $new_user_id, 'user_email_verification_code_for_link', $user_email_verification_code_for_link );
                                        update_user_meta( $new_user_id, 'nickname', $uEmail_secure );

                                        $arr_for_link = array(
                                            'action' => 'verify_email',
                                            'user_id' => $new_user_id,
                                            'verification_code' => $user_email_verification_code_for_link,
                                        );
                                        $json_for_link = json_encode($arr_for_link);
                                        $encrypted_for_link = custom_encrypt_decrypt('encrypt', $json_for_link);

                                        $emails = get_field('emails', 'options');
                                        $search = array(
                                            '[button]',
                                            '[session]'
                                        );
                                        $replace = array(
                                            get_email_part('button', array(
                                                'link' => DO_URL . $encrypted_for_link,
                                                'title' => __('Confirm my Email', TEXTDOMAIN)
                                            )),
                                            get_session_info($_SERVER['REMOTE_ADDR'])
                                        );

                                        $content = Timber::compile( 'email/email.twig', array(
                                            'TEXTDOMAIN' => TEXTDOMAIN,
                                            'BLOGINFO_NAME' => BLOGINFO_NAME,
                                            'BLOGINFO_URL' => BLOGINFO_URL,
                                            'subject' => $emails['auth']['sign_up_subject'],
                                            'text' => str_replace($search, $replace, $emails['auth']['sign_up_text'])
                                        ));
                                        send_email($uEmail_secure, $emails['auth']['sign_up_subject'], $content);

                                        $new_user = get_user_by('id', $new_user_id);
                                        wp_set_current_user( $new_user->ID, $new_user->user_login );
                                        wp_set_auth_cookie( $new_user->ID, true );
                                        do_action( 'wp_login', $new_user->user_login );

                                        wp_redirect( get_page_link_by_page_option_name('profile_page') );
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

        } elseif ($path_segments[0] == $options['forgot_password']['url']){

            $template = 'forgot-password.twig';
            $title = __('Forgot Password', TEXTDOMAIN);
            $text = $options['forgot_password']['text'];
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

                            $emails = get_field('emails', 'options');

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
                                'subject' => $emails['auth']['reset_password_request_subject'],
                                'text' => str_replace($search, $replace, $emails['auth']['reset_password_request_text'])
                            ));
                            send_email($uEmail_secure, $emails['auth']['reset_password_request_subject'], $content);

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

        }

        $context['title'] = $title;
        $context['text'] = $text;
        Timber::render( 'auth/' . $template, $context );
        exit;
    }

}
add_action( 'init', 'custom_system_auth_pages_callback' );
