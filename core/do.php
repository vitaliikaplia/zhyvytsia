<?php

if(!defined('ABSPATH')){exit;}

/** Custom "do" page */
function custom_system_do_page_callback() {

    /** defining staff */
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));
    $general_fields = cache_general_fields();
    $profile_url = BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/';
    $login_url = BLOGINFO_URL . '/' . $general_fields['auth']['login']['url'] . '/';
    $password_reset_url = BLOGINFO_URL . '/' . $general_fields['auth']['password_reset']['url'] . '/';

    /** checking 'do' url */
    if (isset($path_segments[0]) && $path_segments[0] === 'do') {

        /** checking suffix url */
        if(!isset($path_segments[1]) && !$path_segments[1]) {
            wp_redirect(BLOGINFO_URL);
            exit;
        }

        /** validating suffix url */
        if(!($decrypted = custom_encrypt_decrypt('decrypt', trim($path_segments[1])))){
            wp_redirect(BLOGINFO_URL);
            exit;
        }

        /** change server response */
        add_action('wp', function(){ status_header( 200 ); });

        /** getting data */
        $arr_data = json_decode($decrypted, true);

        /** do stuff */
        switch ($arr_data['action']) {
            case "verify_email":
                if(
                    ( $user_id = intval(stripslashes($arr_data['user_id'])) )
                    &&
                    ( $verification_code = intval(stripslashes($arr_data['verification_code'])) )
                    &&
                    get_user_meta($user_id, "user_email_verification_code_for_link", true) == $verification_code
                    &&
                    get_user_meta($user_id, "user_email_confirmed", true) != true
                ){
                    update_user_meta( $user_id, 'user_email_confirmed', true );
                    $user_info = get_userdata($user_id);
                    $user_email = $user_info->user_email;
                    $search = array(
                        '[button]',
                        '[session]'
                    );
                    $replace = array(
                        get_email_part('button', array(
                            'link' => BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/',
                            'title' => __('Go to my profile', TEXTDOMAIN)
                        )),
                        get_session_info($_SERVER['REMOTE_ADDR'])
                    );
                    $content = Timber::compile( 'email/email.twig', array(
                        'TEXTDOMAIN' => TEXTDOMAIN,
                        'BLOGINFO_NAME' => BLOGINFO_NAME,
                        'BLOGINFO_URL' => BLOGINFO_URL,
                        'subject' => $general_fields['emails']['auth']['user_confirmation_subject'],
                        'text' => str_replace($search, $replace, $general_fields['emails']['auth']['user_confirmation_text'])
                    ));
                    send_email($user_email, $general_fields['emails']['auth']['user_confirmation_subject'], $content);
                    add_notify('success', __('Your email verified successfully, thanks!', TEXTDOMAIN));
                    if(is_user_logged_in()){
                        wp_redirect( $profile_url );
                    } else {
                        wp_redirect( $login_url );
                    }
                    break;
                }
            case "password_recovery_request":
                if(
                    ( $user_id = intval(stripslashes($arr_data['user_id'])) )
                    &&
                    ( $password_recovery_code_for_link = intval(stripslashes($arr_data['password_recovery_code_for_link'])) )
                    &&
                    get_user_meta($user_id, "password_recovery_code_for_link", true) == $password_recovery_code_for_link
                    &&
                    isset($arr_data['nonce']) && wp_verify_nonce(sanitize_text_field($arr_data['nonce']), 'reset-password-request')
                ){
                    $user = get_userdata($user_id);
                    $arr_for_json['u_email'] = $user->user_email;
                    $arr_for_json['request_redirect_nonce'] = wp_create_nonce('request-redirect');
                    $arr_for_json['password_recovery_code_for_link'] = $password_recovery_code_for_link;
                    $json = json_encode($arr_for_json);
                    $password_reset_url = $password_reset_url . custom_encrypt_decrypt('encrypt', $json);
                    wp_redirect($password_reset_url);
                } else {
                    add_notify('warning', __('Your link is out of date or invalid', TEXTDOMAIN));
                    if(is_user_logged_in()){
                        wp_redirect( $profile_url );
                    } else {
                        wp_redirect( $login_url );
                    }
                }
                break;
            default:
                wp_redirect(BLOGINFO_URL);
                break;
        }

    }

}
add_action( 'init', 'custom_system_do_page_callback' );
