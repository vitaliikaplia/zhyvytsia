<?php

if(!defined('ABSPATH')){exit;}

/**
 * Template name: Login
 */

if(is_user_logged_in()){

    wp_redirect( BLOGINFO_URL );
    exit;

} else {

    $context = Timber::context();
    setcookie( '2fa', '', time() - DAY_IN_SECONDS, '/' );
    setcookie( 'recovery', '', time() - DAY_IN_SECONDS, '/' );

    if (isset($_POST['uEmail']) && isset($_POST['uPassword']) && isset($_POST['nonce'])) {

        $nonce = sanitize_text_field($_POST['nonce']);

        if (wp_verify_nonce($nonce, 'login')) {

            $uEmail_trim = trim($_POST['uEmail']);
            $uEmail_secure = htmlspecialchars($uEmail_trim, ENT_QUOTES, 'UTF-8');

            if(filter_var($uEmail_secure, FILTER_VALIDATE_EMAIL) && strlen($uEmail_secure) < 60){

                $user = get_user_by('email', $uEmail_secure);

                if($user){

                    $password = wp_unslash($_POST['uPassword']);

                    if(strlen($password) < 60){

                        if($user->ID && wp_check_password( $password, $user->data->user_pass )){

                            $two_factor_authorization = get_user_meta($user->ID, 'two_factor_authorization', true);

                            if($two_factor_authorization == 'sms' || $two_factor_authorization == 'email'){

                                $two_factor_code = random_int(100000, 999999);
                                update_user_meta($user->ID, '2fa_code', $two_factor_code);

                                if($two_factor_authorization == 'sms'){
                                    if( ($user_phone = get_user_meta($user->ID, 'user_phone', true)) && get_user_meta($user->ID, 'user_phone_confirmed', true) ){
                                        $sms_message = __("Your secret 2FA code", TEXTDOMAIN) . ': ' . emoji_numbers($two_factor_code);
                                        send_sms(fix_phone_format($user_phone), $sms_message);
                                    }
                                } elseif($two_factor_authorization == 'email'){
                                    $content = Timber::compile( 'email/email-2fa.twig', array(
                                        'TEXTDOMAIN' => TEXTDOMAIN,
                                        'title' => __("2FA Code", TEXTDOMAIN),
                                        //'preheader' => __('Your secret 2FA code', TEXTDOMAIN) . ':',
                                        'two_factor_code' => emoji_numbers($two_factor_code)
                                    ));
                                    send_email($uEmail_secure, __("2FA Code", TEXTDOMAIN), $content);
                                }

                                $arr_params = array(
                                    'type'  => $two_factor_authorization,
                                    'email'  => $uEmail_secure,
                                    'password' => $password,
                                    'uid' => $user->ID,
                                    'two_factor_code' => $two_factor_code,
                                    'nonce' => wp_create_nonce('2fa_page')
                                );

                                $json_params = json_encode($arr_params);
                                $encrypted_params = custom_encrypt_decrypt('encrypt', $json_params);
                                setcookie( '2fa', $encrypted_params, time() + 1 * DAY_IN_SECONDS, '/' );

                                wp_redirect( get_page_link_by_page_option_name('2fa_page') );
                                exit;

                            } else {

                                if(isset($_COOKIE['color_scheme'])){
                                    if($_COOKIE['color_scheme'] == 'dark'){
                                        $color_scheme_class = 'dark';
                                    } else {
                                        $color_scheme_class = 'light';
                                    }
                                    update_user_meta( $user->ID, 'user_appearance', $color_scheme_class );
                                    setcookie( 'color_scheme', '', time() - DAY_IN_SECONDS, '/' );
                                }

                                if(isset($_COOKIE['anonymous_locale']) && strlen($_COOKIE['anonymous_locale']) < 5){
                                    update_user_meta( $user->ID, 'locale', stripslashes($_COOKIE['anonymous_locale']) );
                                    setcookie( 'anonymous_locale', '', time() - DAY_IN_SECONDS, '/' );
                                }

                                delete_user_meta($user->ID, '2fa_code');

                                wp_set_current_user( $user->ID, $user->user_login );
                                wp_set_auth_cookie( $user->ID, true );
                                do_action( 'wp_login', $user->user_login );

                                wp_redirect( BLOGINFO_URL );
                                exit;

                            }
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
                $context['notify'] = add_notify('error', __('Email is not valid!', TEXTDOMAIN), true);
            }

        } else {
            $context['notify'] = add_notify('error', __('Hacker?', TEXTDOMAIN), true);
        }
    }

    Timber::render( array( 'page-login.twig' ), $context );

}
