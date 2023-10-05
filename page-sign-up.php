<?php

if(!defined('ABSPATH')){exit;}

/**
 * Template name: Реєстрація
 */

if(is_user_logged_in()){

    wp_redirect( BLOGINFO_URL );
    exit;

} else {

    $context = Timber::context();

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

                                $user_email_verification_code = random_int(100000, 999999);
                                $user_email_verification_code_for_link = random_int(1000000000, 9999999999);

                                update_user_meta( $new_user_id, 'user_email_verification_code', $user_email_verification_code );
                                update_user_meta( $new_user_id, 'user_email_verification_code_for_link', $user_email_verification_code_for_link );

                                update_user_meta( $new_user_id, 'nickname', $uEmail_secure );

                                $arr_for_link = array(
                                    'user_id' => $new_user_id,
                                    'verification_code' => $user_email_verification_code_for_link,
                                );
                                $json_for_link = json_encode($arr_for_link);
                                $encrypted_for_link = custom_encrypt_decrypt('encrypt', $json_for_link);

                                $emails = get_field('emails', 'options');
                                $search = array(
                                    '[button]',
                                    '[code]',
                                    '[session]'
                                );
                                $replace = array(
                                    get_email_part('button', array(
                                        'link' => BLOGINFO_URL . "/?data=".$encrypted_for_link,
                                        'title' => __('Confirm my Email', TEXTDOMAIN)
                                    )),
                                    emoji_numbers($user_email_verification_code),
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

    $post = new TimberPost();
    $context['post'] = $post;
    $context['fields'] = get_fields();
    Timber::render( array( 'page-sign-up.twig' ), $context );

}
