<?php

if(!defined('ABSPATH')){exit;}

/**
 * Template name: Забули свій пароль?
 */

if(is_user_logged_in()){

    wp_redirect( BLOGINFO_URL );
    exit;

} else {

    $context = Timber::context();

    if (isset($_POST['uEmail']) && isset($_POST['nonce'])) {

        $nonce = sanitize_text_field($_POST['nonce']);

        if (wp_verify_nonce($nonce, 'forgot-password')) {
            $uEmail_secure = htmlspecialchars(trim($_POST['uEmail']), ENT_QUOTES, 'UTF-8');
            if(filter_var($uEmail_secure, FILTER_VALIDATE_EMAIL) && strlen($uEmail_secure) < 60){
                $user = get_user_by('email', $uEmail_secure);
                if($user){

                    $password_recovery_code = random_int(100000, 999999);
                    $password_recovery_code_for_link = random_int(1000000000, 9999999999);

                    update_user_meta($user->ID, 'password_recovery_code', $password_recovery_code);
                    update_user_meta($user->ID, 'password_recovery_code_for_link', $password_recovery_code_for_link);

                    $reset_request_nonce = wp_create_nonce('reset-password-request');

                    $arr_for_link = array(
                        'user_id' => $user->ID,
                        'password_recovery_code_for_link' => $password_recovery_code_for_link,
                        'nonce' => $reset_request_nonce
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
                            'title' => __('Change my password', TEXTDOMAIN)
                        )),
                        emoji_numbers($password_recovery_code),
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

    $post = new TimberPost();
    $context['post'] = $post;
    $context['fields'] = get_fields();
    Timber::render( array( 'page-forgot-password.twig' ), $context );

}
