<?php

if(!defined('ABSPATH')){exit;}

/**
 * Template name: Вхід
 */

if(is_user_logged_in()){

    wp_redirect( BLOGINFO_URL );
    exit;

} else {

    $context = Timber::context();

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

    $post = new TimberPost();
    $context['post'] = $post;
    $context['fields'] = get_fields();
    Timber::render( array( 'page-login.twig' ), $context );

}
