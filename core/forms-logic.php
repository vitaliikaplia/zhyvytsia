<?php

if(!defined('ABSPATH')){exit;}

function custom_system_forms_logic_callback() {

    /** defining stuff */
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));
    $general_fields = cache_general_fields();

    /** defining urls */
    $login_url = BLOGINFO_URL . '/' . $general_fields['auth']['login']['url'] . '/';
    $sign_up_url = BLOGINFO_URL . '/' . $general_fields['auth']['sign_up']['url'] . '/';
    $forgot_password_url = BLOGINFO_URL . '/' . $general_fields['auth']['forgot_password']['url'] . '/';
    $password_reset_url = BLOGINFO_URL . '/' . $general_fields['auth']['password_reset']['url'] . '/';
    $profile_url = BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/';
    $checkout_url = BLOGINFO_URL . '/' . $general_fields['shop']['checkout_page_url'] . '/';

    /** auth: login logic */
    if($path_segments[0] == $general_fields['auth']['login']['url'] && isset($_POST['nonce']) && isset($_POST['u_email']) && isset($_POST['u_password'])) {

        /** checking nonce */
        if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'login')){
            add_notify('error', __('Nonce is wrong', TEXTDOMAIN));
            wp_redirect( $login_url );
            exit;
        }

        /** define email */
        $u_email_secure = wp_unslash(htmlspecialchars(trim($_POST['u_email']), ENT_QUOTES, 'UTF-8'));

        /** checking email length */
        if(strlen($u_email_secure) >= 60){
            add_notify('error', __('Email is to long', TEXTDOMAIN));
            wp_redirect( $login_url );
            exit;
        }

        /** validating email */
        if(!filter_var($u_email_secure, FILTER_VALIDATE_EMAIL)){
            add_notify('error', __('Email is wrong', TEXTDOMAIN));
            wp_redirect( $login_url );
            exit;
        }

        /** adding some decoded post data to url */
        if(!empty($_POST)){
            $arr_for_json['u_email'] = $_POST['u_email'];
            $json = json_encode($arr_for_json);
            $login_url .= custom_encrypt_decrypt('encrypt', $json);
        }

        /** define user */
        $user = get_user_by('email', $u_email_secure);

        /** checking if user exist */
        if(!$user && !$user->ID){
            add_notify('error', __('No user found for this email', TEXTDOMAIN));
            wp_redirect( $login_url );
            exit;
        }

        /** define password */
        $password = wp_unslash($_POST['u_password']);

        /** checking password length */
        if(strlen($password) >= 60){
            add_notify('error', __('Password is to long', TEXTDOMAIN));
            wp_redirect( $login_url );
            exit;
        }

        /** checking if password correct */
        if(!wp_check_password( $password, $user->data->user_pass )){
            add_notify('error', __('Email address/password do not match.', TEXTDOMAIN));
            wp_redirect( $login_url );
            exit;
        }

        /** authorise user */
        wp_set_current_user( $user->ID, $user->user_login );
        wp_set_auth_cookie( $user->ID, true );
        do_action( 'wp_login', $user->user_login );
        delete_user_meta($user->ID, 'password_recovery_code_for_link');

        /** send authorization security letter if needed */
        if($general_fields['emails']['send_authorization_security_letters']){
            $search = array(
                '[session]'
            );
            $replace = array(
                get_session_info(getUserIP())
            );
            $content = Timber::compile( 'email/email.twig', array(
                'TEXTDOMAIN' => TEXTDOMAIN,
                'BLOGINFO_NAME' => BLOGINFO_NAME,
                'BLOGINFO_URL' => BLOGINFO_URL,
                'subject' => $general_fields['emails']['auth']['login_subject'],
                'text' => str_replace($search, $replace, $general_fields['emails']['auth']['login_text'])
            ));
            send_email($u_email_secure, $general_fields['emails']['auth']['login_subject'], $content);
        }

        /** redirecting to profile url after authorisation */
        wp_redirect( $profile_url );
        exit;

    }

    /** auth: sign up logic */
    if($path_segments[0] == $general_fields['auth']['sign_up']['url'] && isset($_POST['nonce']) && isset($_POST['u_email']) && isset($_POST['u_password'])) {

        /** checking nonce */
        if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'sign-up')){
            add_notify('error', __('Nonce is wrong', TEXTDOMAIN));
            wp_redirect( $sign_up_url );
            exit;
        }

        /** define email */
        $u_email_secure = wp_unslash(htmlspecialchars(trim($_POST['u_email']), ENT_QUOTES, 'UTF-8'));

        /** checking email length */
        if(strlen($u_email_secure) >= 60){
            add_notify('error', __('Email is to long', TEXTDOMAIN));
            wp_redirect( $sign_up_url );
            exit;
        }

        /** validating email */
        if(!filter_var($u_email_secure, FILTER_VALIDATE_EMAIL)){
            add_notify('error', __('Email is wrong', TEXTDOMAIN));
            wp_redirect( $sign_up_url );
            exit;
        }

        /** adding some decoded post data to url */
        if(!empty($_POST)){
            $arr_for_json['u_email'] = $_POST['u_email'];
            $json = json_encode($arr_for_json);
            $sign_up_url .= custom_encrypt_decrypt('encrypt', $json);
        }

        /** define user */
        $user = get_user_by('email', $u_email_secure);

        /** check exist user */
        if($user && $user->ID){
            add_notify('error', __('This email address is already taken', TEXTDOMAIN));
            wp_redirect( $sign_up_url );
            exit;
        }

        /** define password */
        $password = wp_unslash($_POST['u_password']);

        /** checking password length */
        if(strlen($password) >= 60){
            add_notify('error', __('Password is to long', TEXTDOMAIN));
            wp_redirect( $sign_up_url );
            exit;
        }

        /** define password strength */
        $password_strength = check_password_strength($password);

        /** checking password strength */
        if($password_strength != 'ok'){
            add_notify('error', $password_strength);
            wp_redirect( $sign_up_url );
            exit;
        }

        /** adding new user */
        $user_data_arr = array(
            'user_login'    => $u_email_secure,
            'user_pass'	    => $password,
            'user_email'    => $u_email_secure,
            'role'		    => 'client'
        );
        $new_user_id = wp_insert_user( $user_data_arr );
        if($new_user_id){
            $user_email_verification_code_for_link = random_int(1000000000, 9999999999);
            $user_email_verification_code = random_int(1000, 9999);
            update_user_meta( $new_user_id, 'user_email_verification_code_for_link', $user_email_verification_code_for_link );
            update_user_meta( $new_user_id, 'user_email_verification_code', $user_email_verification_code );
            update_user_meta( $new_user_id, 'nickname', $u_email_secure );
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
                get_session_info(getUserIP())
            );
            $content = Timber::compile( 'email/email.twig', array(
                'TEXTDOMAIN' => TEXTDOMAIN,
                'BLOGINFO_NAME' => BLOGINFO_NAME,
                'BLOGINFO_URL' => BLOGINFO_URL,
                'subject' => $general_fields['emails']['auth']['sign_up_subject'],
                'text' => str_replace($search, $replace, $general_fields['emails']['auth']['sign_up_text'])
            ));
            send_email($u_email_secure, $general_fields['emails']['auth']['sign_up_subject'], $content);
            $new_user = get_user_by('id', $new_user_id);
            wp_set_current_user( $new_user->ID, $new_user->user_login );
            wp_set_auth_cookie( $new_user->ID, true );
            do_action( 'wp_login', $new_user->user_login );
            add_notify('success', __('Congratulations! Your new account has been created! Check your Email for confirmation.', TEXTDOMAIN));
            wp_redirect( $profile_url );
            exit;
        }
    }

    /** auth: forgot password logic */
    if($path_segments[0] == $general_fields['auth']['forgot_password']['url'] && isset($_POST['nonce']) && isset($_POST['u_email'])) {

        /** checking nonce */
        if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'forgot-password')){
            add_notify('error', __('Nonce is wrong', TEXTDOMAIN));
            wp_redirect( $forgot_password_url );
            exit;
        }

        /** define email */
        $u_email_secure = wp_unslash(htmlspecialchars(trim($_POST['u_email']), ENT_QUOTES, 'UTF-8'));

        /** checking email length */
        if(strlen($u_email_secure) >= 60){
            add_notify('error', __('Email is to long', TEXTDOMAIN));
            wp_redirect( $forgot_password_url );
            exit;
        }

        /** validating email */
        if(!filter_var($u_email_secure, FILTER_VALIDATE_EMAIL)){
            add_notify('error', __('Email is wrong', TEXTDOMAIN));
            wp_redirect( $forgot_password_url );
            exit;
        }

        /** adding some decoded post data to url */
        if(!empty($_POST)){
            $arr_for_json['u_email'] = $_POST['u_email'];
            $json = json_encode($arr_for_json);
            $forgot_password_url .= custom_encrypt_decrypt('encrypt', $json);
        }

        /** define user */
        $user = get_user_by('email', $u_email_secure);

        /** checking if user exist */
        if(!$user && !$user->ID){
            add_notify('error', __('No account found for this email', TEXTDOMAIN));
            wp_redirect( $forgot_password_url );
            exit;
        }

        /** processing password changing request */
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
            get_session_info(getUserIP())
        );
        $content = Timber::compile( 'email/email.twig', array(
            'TEXTDOMAIN' => TEXTDOMAIN,
            'BLOGINFO_NAME' => BLOGINFO_NAME,
            'BLOGINFO_URL' => BLOGINFO_URL,
            'subject' => $general_fields['emails']['auth']['reset_password_request_subject'],
            'text' => str_replace($search, $replace, $general_fields['emails']['auth']['reset_password_request_text'])
        ));
        send_email($u_email_secure, $general_fields['emails']['auth']['reset_password_request_subject'], $content);
        add_notify('success', __('Verification code sent', TEXTDOMAIN));
        wp_redirect( BLOGINFO_URL . '/' . $general_fields['auth']['forgot_password']['url'] . '/' );
        exit;
    }

    /** auth: change password logic */
    if($path_segments[0] == $general_fields['auth']['password_reset']['url'] && isset($_POST['nonce']) && isset($_POST['u_n_password']) && isset($_POST['u_email']) && isset($_POST['request_redirect_nonce']) && isset($_SERVER['REQUEST_URI'])) {

        /** Hacker? */
        $decrypted = custom_encrypt_decrypt('decrypt', trim($path_segments[1]));
        $arr_data = json_decode($decrypted, true);
        if(
            $arr_data['u_email'] != wp_unslash(htmlspecialchars(trim($_POST['u_email']), ENT_QUOTES, 'UTF-8'))
            ||
            $arr_data['request_redirect_nonce'] != sanitize_text_field($_POST['request_redirect_nonce'])
        ){
            add_notify('error', __('Hacker?', TEXTDOMAIN));
            wp_redirect( $login_url );
            exit;
        }

        /** checking 'password-reset' nonce */
        if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'password-reset')){
            add_notify('error', __('Nonce \'password-reset\' is wrong', TEXTDOMAIN));
            wp_redirect( $forgot_password_url );
            exit;
        }

        /** checking 'request-redirect' nonce */
        if(!wp_verify_nonce(sanitize_text_field($_POST['request_redirect_nonce']), 'request-redirect')){
            add_notify('error', __('Nonce \'request-redirect\' is wrong', TEXTDOMAIN));
            wp_redirect( $forgot_password_url );
            exit;
        }

        /** define email */
        $u_email_secure = wp_unslash(htmlspecialchars(trim($_POST['u_email']), ENT_QUOTES, 'UTF-8'));

        /** checking email length */
        if(strlen($u_email_secure) >= 60){
            add_notify('error', __('Email is to long', TEXTDOMAIN));
            wp_redirect( $forgot_password_url );
            exit;
        }

        /** validating email */
        if(!filter_var($u_email_secure, FILTER_VALIDATE_EMAIL)){
            add_notify('error', __('Email is wrong', TEXTDOMAIN));
            wp_redirect( $forgot_password_url );
            exit;
        }

        /** define user */
        $user = get_user_by('email', $u_email_secure);

        /** checking if user exist */
        if(!$user && !$user->ID){
            add_notify('error', __('Hm, no user found', TEXTDOMAIN));
            wp_redirect( $forgot_password_url );
            exit;
        }

        /** checking recovery code */
        if(
            ! ( $password_recovery_code_for_link = intval(stripslashes($arr_data['password_recovery_code_for_link'])) )
            ||
            ! get_user_meta($user->ID, "password_recovery_code_for_link", true) == $password_recovery_code_for_link
        ){
            add_notify('error', __('Wrong recovery code', TEXTDOMAIN));
            wp_redirect( $forgot_password_url );
            exit;
        }

        /** define password */
        $password = wp_unslash($_POST['u_n_password']);

        /** checking password length */
        if(strlen($password) >= 60){
            add_notify('error', __('Password is to long', TEXTDOMAIN));
            wp_redirect( $password_reset_url . $path_segments[1] . '/' );
            exit;
        }

        /** define password strength */
        $password_strength = check_password_strength($password);

        /** checking password strength */
        if($password_strength != 'ok'){
            add_notify('error', $password_strength);
            wp_redirect( $password_reset_url . $path_segments[1] . '/' );
            exit;
        }

        /** checking same password */
        if(wp_check_password( $password, $user->data->user_pass )){
            add_notify('warning', __('Looks like the new password is the same', TEXTDOMAIN));
            wp_redirect( $password_reset_url . $path_segments[1] . '/' );
            exit;
        }

        /** processing to change password */
        $search = array(
            '[session]'
        );
        $replace = array(
            get_session_info(getUserIP())
        );
        $content = Timber::compile( 'email/email.twig', array(
            'TEXTDOMAIN' => TEXTDOMAIN,
            'BLOGINFO_NAME' => BLOGINFO_NAME,
            'BLOGINFO_URL' => BLOGINFO_URL,
            'subject' => $general_fields['emails']['auth']['password_change_subject'],
            'text' => str_replace($search, $replace, $general_fields['emails']['auth']['password_change_text'])
        ));
        send_email($user->user_email, $general_fields['emails']['auth']['password_change_subject'], $content);
        wp_set_password( $password, $user->ID );
        delete_user_meta($user->ID, 'password_recovery_code_for_link');
        add_notify('success', __('The password has been changed', TEXTDOMAIN));
        wp_redirect( $login_url );
        exit;
    }

    /** profile: edit profile */
    if(isset($path_segments[0]) && isset($path_segments[1]) && $path_segments[0] == $general_fields['profile']['url'] && $path_segments[1] == 'edit' && isset($_POST['nonce']) && isset($_GET['profile']) && $_GET['profile'] == 'update'){

        /** checking nonce */
        if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'profile-update')){
            add_notify('error', __('Nonce is wrong', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** define notify summary */
        $notify = "";

        /** define user id */
        $user_id = get_current_user_id();

        /** updating first name */
        if (isset($_POST['f_name']) && $_POST['f_name']) {
            if($_POST['f_name'] != get_user_meta($user_id, 'first_name', true)){
                update_user_meta( $user_id, 'first_name', htmlspecialchars($_POST['f_name'], ENT_QUOTES, 'UTF-8') );
                $notify .= __('First name updated', TEXTDOMAIN) . '<br>';
            } else {
                $notify .= __('First name not updated', TEXTDOMAIN) . '<br>';
            }
        } else {
            if($_POST['f_name'] != get_user_meta($user_id, 'first_name', true)){
                update_user_meta( $user_id, 'first_name', false );
                $notify .= __('First name removed', TEXTDOMAIN) . '<br>';
            } else {
                $notify .= __('First name not updated', TEXTDOMAIN) . '<br>';
            }
        }

        /** updating last name */
        if (isset($_POST['l_name']) && $_POST['l_name']) {
            if($_POST['l_name'] != get_user_meta($user_id, 'last_name', true)){
                update_user_meta( $user_id, 'last_name', htmlspecialchars($_POST['l_name'], ENT_QUOTES, 'UTF-8') );
                $notify .= __('Last name updated', TEXTDOMAIN) . '<br>';
            } else {
                $notify .= __('Last name not updated', TEXTDOMAIN) . '<br>';
            }
        } else {
            if($_POST['l_name'] != get_user_meta($user_id, 'last_name', true)){
                update_user_meta( $user_id, 'last_name', false );
                $notify .= __('Last name removed', TEXTDOMAIN) . '<br>';
            } else {
                $notify .= __('Last name not updated', TEXTDOMAIN) . '<br>';
            }
        }

        /** updating user phone */
        if (isset($_POST['user_phone']) && $_POST['user_phone']) {
            if(check_phone(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8'))){
                $user_phone = get_user_meta($user_id, 'user_phone', true);
                if($user_phone != fix_phone_format(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8'))){
                    $uhp = get_users(array(
                        'meta_key' => 'user_phone',
                        'meta_value' => fix_phone_format(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8'))
                    ));
                    if(empty($uhp)){
                        update_user_meta( $user_id, 'user_phone_confirmed', false );
                        update_user_meta( $user_id, 'user_phone', fix_phone_format(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8')));
                        $user_sms_verification_code = random_int(1000, 9999);
                        update_user_meta( $user_id, 'user_sms_verification_code', $user_sms_verification_code );
                        $sms_message = __("Your verification code:", TEXTDOMAIN) . ' ' . emoji_numbers($user_sms_verification_code);
                        send_sms(fix_phone_format(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8')), $sms_message);
                        $notify .= __('User phone updated', TEXTDOMAIN);
                    } else {
                        $notify .= __('This phone number is already taken', TEXTDOMAIN);
                    }
                } else {
                    $notify .= __('User phone not updated', TEXTDOMAIN);
                }
            } else {
                $notify .= __('Enter valid phone number', TEXTDOMAIN);
            }
        } else {
            update_user_meta( $user_id, 'user_phone', false );
            update_user_meta( $user_id, 'user_phone_confirmed', false );
            $notify .= __('Phone number removed', TEXTDOMAIN);
        }
        add_notify('success', $notify);
        wp_redirect( $profile_url . $path_segments[1] . '/' );
        exit;

    }

    /** profile: change email */
    if(isset($path_segments[0]) && isset($path_segments[1]) && $path_segments[0] == $general_fields['profile']['url'] && $path_segments[1] == 'change-email' && isset($_POST['nonce']) && isset($_GET['email']) && $_GET['email'] == 'change' && isset($_POST['u_email']) && $_POST['u_email'] && isset($_POST['u_password']) && $_POST['u_password']){

        /** checking nonce */
        if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'email-change')){
            add_notify('error', __('Nonce is wrong', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** define user & data */
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);
        $user_email = $user->user_email;
        $new_u_email_trim = trim($_POST['u_email']);
        $new_u_email_secure = wp_unslash(htmlspecialchars($new_u_email_trim, ENT_QUOTES, 'UTF-8'));

        /** define passwords */
        $password = wp_unslash($_POST['u_password']);

        /** checking password length */
        if(strlen($password) >= 60){
            add_notify('warning', __('Password is to long', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** validate password */
        if(!wp_check_password( $password, $user->data->user_pass )){
            add_notify('error', __('Wrong password', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** checking email length */
        if(strlen($new_u_email_secure) >= 60){
            add_notify('warning', __('Email is to long', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** comparing emails */
        if($new_u_email_secure == $user_email){
            add_notify('warning', __('New Email is the same', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** changing email */
        $args = array(
            'ID'         => $user_id,
            'user_email' => $new_u_email_secure
        );
        wp_update_user($args);
        update_user_meta( $user_id, 'user_email_confirmed', false );
        add_notify('success', __('Email successfully changed', TEXTDOMAIN));
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
            get_session_info(getUserIP())
        );
        $content = Timber::compile( 'email/email.twig', array(
            'TEXTDOMAIN' => TEXTDOMAIN,
            'BLOGINFO_NAME' => BLOGINFO_NAME,
            'BLOGINFO_URL' => BLOGINFO_URL,
            'subject' => $general_fields['emails']['auth']['sign_up_subject'],
            'text' => str_replace($search, $replace, $general_fields['emails']['auth']['sign_up_text'])
        ));
        send_email($new_u_email_secure, $general_fields['emails']['auth']['sign_up_subject'], $content);
        wp_redirect( $profile_url . $path_segments[1] . '/' );
        exit;
    }

    /** profile: change password */
    if(isset($path_segments[0]) && isset($path_segments[1]) && $path_segments[0] == $general_fields['profile']['url'] && $path_segments[1] == 'change-password' && isset($_POST['nonce']) && isset($_GET['password']) && $_GET['password'] == 'change' && isset($_POST['u_o_password']) && $_POST['u_o_password'] && isset($_POST['u_n_password']) && $_POST['u_n_password']){

        /** checking nonce */
        if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'password-change')){
            add_notify('error', __('Nonce is wrong', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** define user */
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);

        /** define passwords */
        $o_password = wp_unslash($_POST['u_o_password']);
        $n_password = wp_unslash($_POST['u_n_password']);

        /** checking old password length */
        if(strlen($o_password) >= 60){
            add_notify('success', __('Old password: To long', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** define old password strength */
        $o_password_strength = check_password_strength($o_password);

        /** checking old password strength */
        if($o_password_strength != 'ok'){
            add_notify('success', __('Old password', TEXTDOMAIN) . ': ' . $o_password_strength);
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** checking new password length */
        if(strlen($n_password) >= 60){
            add_notify('success', __('New password: To long', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** define new password strength */
        $n_password_strength = check_password_strength($n_password);

        /** checking new password strength */
        if($n_password_strength != 'ok'){
            add_notify('success', __('New password', TEXTDOMAIN) . ': ' . $n_password_strength);
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** validate old password */
        if(!wp_check_password( $o_password, $user->data->user_pass )){
            add_notify('success', __('Wrong old password', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** comparing passwords */
        if($n_password == $o_password){
            add_notify('success', __('New password should be different', TEXTDOMAIN));
            wp_redirect( $profile_url . $path_segments[1] . '/' );
            exit;
        }

        /** changing password */
        $search = array(
            '[session]'
        );
        $replace = array(
            get_session_info(getUserIP())
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
        add_notify('success', __('The password has been changed', TEXTDOMAIN));
        wp_redirect( $profile_url . $path_segments[1] . '/' );
        exit;
    }

    /** checkout */
    if($path_segments[0] == $general_fields['shop']['checkout_page_url'] && isset($_POST['nonce']) && isset($_GET['checkout']) && $_GET['checkout'] == "process"){

        /** checking nonce */
        if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'checkout-process')){
            add_notify('error', __('Nonce is wrong', TEXTDOMAIN));
            wp_redirect( $checkout_url );
            exit;
        }

        /** define email */
        $user_email_secure = wp_unslash(htmlspecialchars(trim($_POST['user_email']), ENT_QUOTES, 'UTF-8'));

        /** checking email length */
        if(strlen($user_email_secure) >= 60){
            add_notify('error', __('Email is to long', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }

        /** validating email */
        if(!filter_var($user_email_secure, FILTER_VALIDATE_EMAIL)){
            add_notify('error', __('Email is wrong', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }

        /** check and define first_name */
        if(!isset($_POST['first_name']) || !$_POST['first_name']){
            add_notify('error', __('First name is not specified', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }
        $first_name = wp_unslash(htmlspecialchars(trim($_POST['first_name']), ENT_QUOTES, 'UTF-8'));

        /** checking first_name length */
        if(strlen($first_name) >= 60){
            add_notify('error', __('Your first name is to long', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }

        /** check and define last_name */
        if(!isset($_POST['last_name']) || !$_POST['last_name']){
            add_notify('error', __('Last name is not specified', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }
        $last_name = wp_unslash(htmlspecialchars(trim($_POST['last_name']), ENT_QUOTES, 'UTF-8'));

        /** checking last_name length */
        if(strlen($last_name) >= 60){
            add_notify('error', __('Your last name is to long', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }

        /** check if user phone specified */
        if(!isset($_POST['user_phone']) || !$_POST['user_phone']){
            add_notify('error', __('Phone number is not specified', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }

        /** check if user phone is valid */
        if(!check_phone(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8'))){
            add_notify('error', __('Phone number is not valid', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }

        /** define user phone */
        $user_phone = fix_phone_format(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8'));

        /** check if delivery type specified */
        if(!isset($_POST['delivery_type']) || !$_POST['delivery_type']){
            add_notify('error', __('Delivery type is not specified', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }

        /** define delivery type */
        $delivery_type = htmlspecialchars($_POST['delivery_type'], ENT_QUOTES, 'UTF-8');

        /** check if payment type specified */
        if(!isset($_POST['payment_type']) || !$_POST['payment_type']){
            add_notify('error', __('Payment type is not specified', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }

        /** define payment type */
        $payment_type = htmlspecialchars($_POST['payment_type'], ENT_QUOTES, 'UTF-8');

        /** check if payment type specified */
        if( ! ( $payment_type == "online_payment" || $payment_type == "cod_payment" || $payment_type == "payment_upon_receipt" || $payment_type == "payment_by_details" ) ){
            add_notify('error', __('Payment type is not specified', TEXTDOMAIN));
            setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
            wp_redirect( $checkout_url );
            exit;
        }

        /** check if delivery data specified */
        if($delivery_type == "up"){

            /** check if address data specified */
            if(
                !isset($_POST['user_region']) || !$_POST['user_region']
                ||
                !isset($_POST['user_city']) || !$_POST['user_city']
                ||
                !isset($_POST['user_zip']) || !$_POST['user_zip']
                ||
                !isset($_POST['user_address']) || !$_POST['user_address']
            ){
                add_notify('error', __('Address is not specified', TEXTDOMAIN));
                setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
                wp_redirect( $checkout_url );
                exit;
            }

        } elseif ($delivery_type == "np"){

            /** check if nova poshta city and office specified */
            if(
                !isset($_POST['user_np_city_ref']) || !$_POST['user_np_city_ref']
                ||
                !isset($_POST['user_np_city_name']) || !$_POST['user_np_city_name']
                ||
                !isset($_POST['user_np_office_number']) || !$_POST['user_np_office_number']
                ||
                !isset($_POST['user_np_office_name']) || !$_POST['user_np_office_name']
            ){
                add_notify('error', __('Nova Poshta city and office is not specified', TEXTDOMAIN));
                setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
                wp_redirect( $checkout_url );
                exit;
            }

        } elseif ($delivery_type == "pu"){

            /** check if self pickup point specified */
            if( !isset($_POST['user_pickup_point']) || !$_POST['user_pickup_point'] ){
                add_notify('error', __('Self pickup point is not specified', TEXTDOMAIN));
                setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
                wp_redirect( $checkout_url );
                exit;
            }

        }

        /** preparing delivery information */
        if($delivery_type == "up" || $delivery_type == "np" || $delivery_type == "pu"){

            /** define address data */
            $user_region = htmlspecialchars($_POST['user_region'], ENT_QUOTES, 'UTF-8');
            $user_city = htmlspecialchars($_POST['user_city'], ENT_QUOTES, 'UTF-8');
            $user_zip = htmlspecialchars($_POST['user_zip'], ENT_QUOTES, 'UTF-8');
            $user_address = htmlspecialchars($_POST['user_address'], ENT_QUOTES, 'UTF-8');

            /** define nova poshta city and office */
            $user_np_city_ref = htmlspecialchars($_POST['user_np_city_ref'], ENT_QUOTES, 'UTF-8');
            $user_np_city_name = htmlspecialchars($_POST['user_np_city_name'], ENT_QUOTES, 'UTF-8');
            $user_np_office_number = htmlspecialchars($_POST['user_np_office_number'], ENT_QUOTES, 'UTF-8');
            $user_np_office_name = htmlspecialchars($_POST['user_np_office_name'], ENT_QUOTES, 'UTF-8');

            /** define self pickup point */
            $user_pickup_point = htmlspecialchars($_POST['user_pickup_point'], ENT_QUOTES, 'UTF-8');

            if($delivery_type == "up"){

                $delivery_information = "<p>" . __('Region (for example, Kyivska, Lvivska, etc.)', TEXTDOMAIN) . ": <b>" . $user_region . "</b><br>";
                $delivery_information .= __('City (or settlement)', TEXTDOMAIN) . ": <b>" . $user_city . "</b><br>";
                $delivery_information .= __('ZIP Code', TEXTDOMAIN) . ": <b>" . $user_zip . "</b><br>";
                $delivery_information .= __('Address (street, house, apartment)', TEXTDOMAIN) . ": <b>" . $user_address . "</b></p>";

            } elseif ($delivery_type == "np"){

                $delivery_information = "<p>" . __('Nova Poshta city name', TEXTDOMAIN) . ": <b>" . $user_np_city_name . "</b><br>";
                $delivery_information .= __('Nova Poshta office name', TEXTDOMAIN) . ": <b>" . $user_np_office_name . "</b><br>";
                $delivery_information .= __('Nova Poshta office number', TEXTDOMAIN) . ": <b>" . $user_np_office_number . "</b></p>";

            } elseif ($delivery_type == "pu"){

                $point_id = intval(str_replace('point-','',$user_pickup_point))-1;
                $delivery_information = "<p>".__('Pickup point', TEXTDOMAIN).": <b>" . $general_fields['shop']['self_pickup_points'][$point_id]['name'] . "</b></p>";

            }

        }

        /** checking if user logged in */
        if(is_user_logged_in()){

            $current_user = wp_get_current_user();
            $order_user_id = $current_user->ID;

            /** update profile options */
            update_user_meta( $order_user_id, 'first_name', $first_name ?? false );
            update_user_meta( $order_user_id, 'last_name', $last_name ?? false );
            update_user_meta( $order_user_id, 'payment_type', $payment_type ?? false );
            update_user_meta( $order_user_id, 'delivery_type', $delivery_type ?? false );
            update_user_meta( $order_user_id, 'user_region', $user_region ?? false );
            update_user_meta( $order_user_id, 'user_city', $user_city ?? false );
            update_user_meta( $order_user_id, 'user_zip', $user_zip ?? false );
            update_user_meta( $order_user_id, 'user_address', $user_address ?? false );
            update_user_meta( $order_user_id, 'user_np_city_ref', $user_np_city_ref ?? false );
            update_user_meta( $order_user_id, 'user_np_city_name', $user_np_city_name ?? false );
            update_user_meta( $order_user_id, 'user_np_office_number', $user_np_office_number ?? false );
            update_user_meta( $order_user_id, 'user_np_office_name', $user_np_office_name ?? false );
            update_user_meta( $order_user_id, 'user_pickup_point', $user_pickup_point ?? false );

        } else {

            /** define user by email */
            $existing_user = get_user_by('email', $user_email_secure);

            /** register new user */
            if(isset($_POST['create_an_account']) && $_POST['create_an_account']){

                /** check user phone is already taken */
                $uhp = get_users(array(
                    'meta_key' => 'user_phone',
                    'meta_value' => $user_phone
                ));
                if(!empty($uhp)){
                    add_notify('error', __('This phone number is already taken', TEXTDOMAIN));
                    setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
                    wp_redirect( $checkout_url );
                    exit;
                }

                /** check if user exist */
                if($existing_user && $existing_user->ID){
                    add_notify('error', __('This email address is already taken', TEXTDOMAIN));
                    setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
                    wp_redirect( $checkout_url );
                    exit;
                }

                /** define new user password */
                $password = wp_generate_password();

                /** preparing new user data */
                $user_data_arr = array(
                    'user_login'    => $user_email_secure,
                    'user_pass'	    => $password,
                    'user_email'    => $user_email_secure,
                    'role'		    => 'client'
                );

                /** adding new user */
                $order_user_id = wp_insert_user( $user_data_arr );

                /** if new user created */
                if($order_user_id){

                    /** set additional profile verification options */
                    $user_email_verification_code_for_link = random_int(1000000000, 9999999999);
                    $user_email_verification_code = random_int(1000, 9999);
                    update_user_meta( $order_user_id, 'user_email_verification_code_for_link', $user_email_verification_code_for_link );
                    update_user_meta( $order_user_id, 'user_email_verification_code', $user_email_verification_code );
                    update_user_meta( $order_user_id, 'nickname', $user_email_secure );

                    /** preparing confirmation link */
                    $arr_for_link = array(
                        'action' => 'verify_email',
                        'user_id' => $order_user_id,
                        'verification_code' => $user_email_verification_code_for_link,
                    );
                    $json_for_link = json_encode($arr_for_link);
                    $encrypted_for_link = custom_encrypt_decrypt('encrypt', $json_for_link);

                    /** preparing email content */
                    $search = array(
                        '[login]',
                        '[password]',
                        '[button]',
                        '[code]',
                        '[session]'
                    );
                    $replace = array(
                        $user_email_secure,
                        $password,
                        get_email_part('button', array(
                            'link' => DO_URL . $encrypted_for_link,
                            'title' => __('Confirm my Email', TEXTDOMAIN)
                        )),
                        emoji_numbers($user_email_verification_code),
                        get_session_info(getUserIP())
                    );
                    $content = Timber::compile( 'email/email.twig', array(
                        'TEXTDOMAIN' => TEXTDOMAIN,
                        'BLOGINFO_NAME' => BLOGINFO_NAME,
                        'BLOGINFO_URL' => BLOGINFO_URL,
                        'subject' => $general_fields['emails']['checkout']['sign_up_subject'],
                        'text' => str_replace($search, $replace, $general_fields['emails']['checkout']['sign_up_text'])
                    ));

                    /** sending email */
                    send_email($user_email_secure, $general_fields['emails']['checkout']['sign_up_subject'], $content);

                    /** set additional profile options */
                    update_user_meta( $order_user_id, 'first_name', $first_name ?? false );
                    update_user_meta( $order_user_id, 'last_name', $last_name ?? false );
                    update_user_meta( $order_user_id, 'user_phone', $user_phone ?? false );

                    /** set more additional profile options */
                    update_user_meta( $order_user_id, 'payment_type', $payment_type ?? false );
                    update_user_meta( $order_user_id, 'delivery_type', $delivery_type ?? false );
                    update_user_meta( $order_user_id, 'user_region', $user_region ?? false );
                    update_user_meta( $order_user_id, 'user_city', $user_city ?? false );
                    update_user_meta( $order_user_id, 'user_zip', $user_zip ?? false );
                    update_user_meta( $order_user_id, 'user_address', $user_address ?? false );
                    update_user_meta( $order_user_id, 'user_np_city_ref', $user_np_city_ref ?? false );
                    update_user_meta( $order_user_id, 'user_np_city_name', $user_np_city_name ?? false );
                    update_user_meta( $order_user_id, 'user_np_office_number', $user_np_office_number ?? false );
                    update_user_meta( $order_user_id, 'user_np_office_name', $user_np_office_name ?? false );
                    update_user_meta( $order_user_id, 'user_pickup_point', $user_pickup_point ?? false );

                    /** preparing and sending sms */
                    $user_sms_verification_code = random_int(1000, 9999);
                    update_user_meta( $order_user_id, 'user_sms_verification_code', $user_sms_verification_code );
                    $sms_message = __("Your verification code:", TEXTDOMAIN) . ' ' . emoji_numbers($user_sms_verification_code);
                    send_sms($user_phone, $sms_message);

                    /** authorising and redirecting new user */
                    $new_user = get_user_by('id', $order_user_id);
                    wp_set_current_user( $new_user->ID, $new_user->user_login );
                    wp_set_auth_cookie( $new_user->ID, true );
                    do_action( 'wp_login', $new_user->user_login );
                    add_notify('success', __('Congratulations! Your new account has been created!', TEXTDOMAIN));

                } else {

                    add_notify('error', __('Error while creating new user', TEXTDOMAIN));
                    setcookie( 'checkout-data', custom_encrypt_decrypt('encrypt', json_encode($_POST)), time() + 1 * DAY_IN_SECONDS, '/' );
                    wp_redirect( $checkout_url );
                    exit;

                }

            } else {

                /** no new user registered */
                $order_user_id = false;

            }

        }

        /** preparing ordered items */
        if(true){
            [
                'ids_arr' => $ids_arr,
                'items' => $items,
                'ids_arr_count_values' => $ids_arr_count_values,
                'ids_arr_count' => $ids_arr_count,
                'ids_arr_unique' => $ids_arr_unique,
                'total_price' => $total_price,
                'total_price_raw' => $total_price_raw,
                'discount' => $discount,
                'ids_arr_count_values_prices' => $ids_arr_count_values_prices
            ] = prepare_positions();
            $context = Timber::context();
            $context['items'] = $items;
            $context['ids_arr_count_values'] = $ids_arr_count_values;
            $context['ids_arr_count'] = $ids_arr_count;
            $context['ids_arr_unique'] = $ids_arr_unique;
            $context['total_price'] = $total_price;
            $ordered_items = Timber::compile( 'dashboard/ordered-items.twig', $context);
        }

        /** placing new order */
        if(true){
            $new_order_args = array(
                'post_type' => 'orders-log',
                'post_title' => 'New order',
                'post_content' => '',
                'post_status' => 'publish'
            );
            $public_order_secret = mt_rand(1, 9);
            for ($i = 1; $i < 50; $i++) {
                $public_order_secret .= mt_rand(0, 9);
            }
            $order_id = wp_insert_post($new_order_args);
            $post_update = array(
                'ID'         => $order_id,
                'post_title' => __("Order", TEXTDOMAIN) . ' #' . $order_id
            );
            wp_update_post( $post_update );
            update_post_meta( $order_id, 'order_status', 'new_order' );
            update_post_meta( $order_id, 'total_price_raw', $total_price_raw );
            update_post_meta( $order_id, 'public_order_secret', $public_order_secret );
            update_post_meta( $order_id, 'discount', $discount );
            update_post_meta( $order_id, 'ids_arr_count_values_prices', $ids_arr_count_values_prices );
            update_post_meta( $order_id, 'ids_arr', $ids_arr );
            update_post_meta( $order_id, 'ids_arr_unique', $ids_arr_unique );
            update_post_meta( $order_id, 'ids_arr_count', $ids_arr_count );
            update_post_meta( $order_id, 'ids_arr_count_values', $ids_arr_count_values );
            update_post_meta( $order_id, 'order_user_id', $order_user_id ?? false );
            update_post_meta( $order_id, 'ordered_items', $ordered_items ?? false );
            update_post_meta( $order_id, 'total_price', $total_price ?? false );
            update_post_meta( $order_id, 'order_user_email', $user_email_secure ?? false );
            update_post_meta( $order_id, 'order_user_first_name', $first_name ?? false );
            update_post_meta( $order_id, 'order_user_last_name', $last_name ?? false );
            update_post_meta( $order_id, 'order_user_phone', nice_phone_format($user_phone) ?? false );
            update_post_meta( $order_id, 'order_user_delivery_type', ['up' => __('UkrPoshta', TEXTDOMAIN), 'np' => __('Nova Poshta', TEXTDOMAIN), 'pu' => __('Pickup', TEXTDOMAIN)][$delivery_type] ?? false );
            update_post_meta( $order_id, 'order_user_payment_type', ['online_payment' => __('Online payment', TEXTDOMAIN), 'cod_payment' => __('COD Payment', TEXTDOMAIN), 'payment_upon_receipt' => __('Payment upon receipt', TEXTDOMAIN), 'payment_by_details' => __('Payment by details', TEXTDOMAIN)][$payment_type] ?? false );
            update_post_meta( $order_id, 'order_user_delivery_information', $delivery_information );
        }

        /** sending email to client */
        if(true){
            $search = array(
                '[order_id]',
                '[ordered_items]',
                '[total_price]',
                '[first_name]',
                '[last_name]',
                '[user_phone]',
                '[user_email]',
                '[payment_type]',
                '[delivery_type]',
                '[delivery_information]',
                '[details_for_payment]',
                '[session]'
            );
            $replace = array(
                $order_id,
                $ordered_items,
                $total_price,
                $first_name,
                $last_name,
                nice_phone_format($user_phone),
                $user_email_secure,
                ['online_payment' => __('Online payment', TEXTDOMAIN), 'cod_payment' => __('COD Payment', TEXTDOMAIN), 'payment_upon_receipt' => __('Payment upon receipt', TEXTDOMAIN), 'payment_by_details' => __('Payment by details', TEXTDOMAIN)][$payment_type] ?? __('Unknown', TEXTDOMAIN),
                ['up' => __('UkrPoshta', TEXTDOMAIN), 'np' => __('Nova Poshta', TEXTDOMAIN), 'pu' => __('Pickup', TEXTDOMAIN)][$delivery_type] ?? __('Unknown', TEXTDOMAIN),
                $delivery_information,
                $general_fields['shop']['details_for_payment'],
                get_session_info(getUserIP())
            );
            $content = Timber::compile( 'email/email.twig', array(
                'TEXTDOMAIN' => TEXTDOMAIN,
                'BLOGINFO_NAME' => BLOGINFO_NAME,
                'BLOGINFO_URL' => BLOGINFO_URL,
                'subject' => str_replace($search, $replace, $general_fields['emails']['checkout']['checkout_subject_user']),
                'text' => str_replace($search, $replace, $general_fields['emails']['checkout']['checkout_text_user'])
            ));
            send_email($user_email_secure, str_replace($search, $replace, $general_fields['emails']['checkout']['checkout_subject_user']), $content);
        }

        /** sending email to admins */
        if(!empty($general_fields['shop']['new_order_email_recipients'])){
            foreach ($general_fields['shop']['new_order_email_recipients'] as $recipient){

                /** preparing email content */
                $search = array(
                    '[order_id]',
                    '[ordered_items]',
                    '[total_price]',
                    '[first_name]',
                    '[last_name]',
                    '[user_phone]',
                    '[user_email]',
                    '[payment_type]',
                    '[delivery_type]',
                    '[delivery_information]',
                    '[session]'
                );
                $replace = array(
                    $order_id,
                    $ordered_items,
                    $total_price,
                    $first_name,
                    $last_name,
                    nice_phone_format($user_phone),
                    $user_email_secure,
                    ['online_payment' => __('Online payment', TEXTDOMAIN), 'cod_payment' => __('COD Payment', TEXTDOMAIN), 'payment_upon_receipt' => __('Payment upon receipt', TEXTDOMAIN), 'payment_by_details' => __('Payment by details', TEXTDOMAIN)][$payment_type] ?? __('Unknown', TEXTDOMAIN),
                    ['up' => __('UkrPoshta', TEXTDOMAIN), 'np' => __('Nova Poshta', TEXTDOMAIN), 'pu' => __('Pickup', TEXTDOMAIN)][$delivery_type] ?? __('Unknown', TEXTDOMAIN),
                    $delivery_information,
                    get_session_info(getUserIP())
                );
                $content = Timber::compile( 'email/email.twig', array(
                    'TEXTDOMAIN' => TEXTDOMAIN,
                    'BLOGINFO_NAME' => BLOGINFO_NAME,
                    'BLOGINFO_URL' => BLOGINFO_URL,
                    'subject' => str_replace($search, $replace, $general_fields['emails']['checkout']['checkout_subject_admin']),
                    'text' => str_replace($search, $replace, $general_fields['emails']['checkout']['checkout_text_admin'])
                ));

                /** sending email */
                send_email($recipient['email'], str_replace($search, $replace, $general_fields['emails']['checkout']['checkout_subject_admin']), $content);

            }
        }

        /** sending sms to admins */
        if($general_fields['shop']['activate_sms_notification_about_new_orders'] && !empty($general_fields['shop']['new_order_sms_recipients'])){
            foreach ($general_fields['shop']['new_order_sms_recipients'] as $recipient){
                /** sending sms */
                if(check_phone($recipient['phone'])){
                    $sms_message = __("New order has been placed", TEXTDOMAIN) . ' #' . $order_id;
                    send_sms(fix_phone_format($recipient['phone']), $sms_message);
                }
            }
        }

        /** preparing redirect url */
        if($payment_type == "online_payment"){
            /** creating new payment and redirection url to bank payment system */
            $redirect_url = prepare_online_payment($order_id, $total_price_raw, $ids_arr_unique, $ids_arr_count_values, $ids_arr_count);
        } else {
            /** preparing new order url, notify message and removing cookie cart */
            add_notify('success', $general_fields['shop']['successful_order_message']);
            setcookie('cart', '', time() - 3600, '/', '.'.BLOGINFO_JUST_DOMAIN);
            if(is_user_logged_in()){
                $redirect_url = $profile_url . 'order/'.$order_id.'/';
            } else {
                $redirect_url = BLOGINFO_URL . '/order/'.$order_id.'/'.$public_order_secret.'/';
            }
        }

        /** redirecting */
        wp_redirect($redirect_url);
        exit;

    }

}
add_action( 'init', 'custom_system_forms_logic_callback' );
