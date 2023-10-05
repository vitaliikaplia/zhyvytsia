<?php

if(!defined('ABSPATH')){exit;}

/** Custom "do" page */
function custom_system_do_page_callback() {

    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));

    if (isset($path_segments[0]) && $path_segments[0] === 'do') {

        if(isset($path_segments[1]) && $path_segments[1]){

            add_action('wp', function(){ status_header( 200 ); });

            if($decrypted = custom_encrypt_decrypt('decrypt', trim($path_segments[1]))){

                $arr_data = json_decode($decrypted, true);

                if(isset($arr_data['action']) && $arr_data['action']){

                    if($arr_data['action'] == "verify_email"){

                        if(($user_id = intval(stripslashes($arr_data['user_id']))) && ($verification_code = intval(stripslashes($arr_data['verification_code']))) && get_user_meta($user_id, "user_email_verification_code_for_link", true) == $verification_code && get_user_meta($user_id, "user_email_confirmed", true) != true){

                            update_user_meta( $user_id, 'user_email_confirmed', true );

                            $user_info = get_userdata($user_id);
                            $user_email = $user_info->user_email;

                            $emails = get_field('emails', 'options');

                            $search = array(
                                '[button]',
                                '[session]'
                            );
                            $replace = array(
                                get_email_part('button', array(
                                    'link' => get_page_link_by_page_option_name('profile_page'),
                                    'title' => __('Go to my profile', TEXTDOMAIN)
                                )),
                                get_session_info($_SERVER['REMOTE_ADDR'])
                            );

                            $content = Timber::compile( 'email/email.twig', array(
                                'TEXTDOMAIN' => TEXTDOMAIN,
                                'BLOGINFO_NAME' => BLOGINFO_NAME,
                                'BLOGINFO_URL' => BLOGINFO_URL,
                                'subject' => $emails['auth']['user_confirmation_subject'],
                                'text' => str_replace($search, $replace, $emails['auth']['user_confirmation_text'])
                            ));
                            send_email($user_email, $emails['auth']['user_confirmation_subject'], $content);

                            wp_redirect( get_page_link_by_page_option_name('profile_page') );
                            exit;

                        } else {

                            wp_redirect(BLOGINFO_URL);
                            exit;

                        }

                    }

                    if($arr_data['action'] == "password_recovery_request"){

                        pr($arr_data);

                    }

                } else {

                    wp_redirect(BLOGINFO_URL);
                    exit;

                }

            } else {

                wp_redirect(BLOGINFO_URL);
                exit;

            }

        } else {

            wp_redirect(BLOGINFO_URL);
            exit;

        }

    }

}
add_action( 'init', 'custom_system_do_page_callback' );
