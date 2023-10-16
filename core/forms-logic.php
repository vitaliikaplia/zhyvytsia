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
                get_session_info($_SERVER['REMOTE_ADDR'])
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
            add_notify('error', __('This email address is already taken!', TEXTDOMAIN));
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
                get_session_info($_SERVER['REMOTE_ADDR'])
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
            get_session_info($_SERVER['REMOTE_ADDR'])
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
        wp_set_password( $password, $user->ID );
        delete_user_meta($user->ID, 'password_recovery_code_for_link');
        add_notify('success', __('The password has been changed', TEXTDOMAIN));
        wp_redirect( $login_url );
        exit;
    }

    /** profile: edit profile */
    if($path_segments[0] == $general_fields['profile']['url'] && $path_segments[1] == 'edit' && isset($_POST['nonce']) && isset($_GET['profile']) && $_GET['profile'] == 'update'){

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
                        'meta_value' => htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8')
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
    if($path_segments[0] == $general_fields['profile']['url'] && $path_segments[1] == 'change-email' && isset($_POST['nonce']) && isset($_GET['email']) && $_GET['email'] == 'change' && isset($_POST['u_email']) && $_POST['u_email'] && isset($_POST['u_password']) && $_POST['u_password']){

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
        wp_redirect( $profile_url . $path_segments[1] . '/' );
        exit;
    }

    /** profile: change password */
    if($path_segments[0] == $general_fields['profile']['url'] && $path_segments[1] == 'change-password' && isset($_POST['nonce']) && isset($_GET['password']) && $_GET['password'] == 'change' && isset($_POST['u_o_password']) && $_POST['u_o_password'] && isset($_POST['u_n_password']) && $_POST['u_n_password']){

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
        add_notify('success', __('The password has been changed', TEXTDOMAIN));
        wp_redirect( $profile_url . $path_segments[1] . '/' );
        exit;
    }

}
add_action( 'init', 'custom_system_forms_logic_callback' );
