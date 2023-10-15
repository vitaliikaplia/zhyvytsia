<?php

if(!defined('ABSPATH')){exit;}

function profile_resend_verification_code_to_email_action(){

    $general_fields = cache_general_fields();

    $to_json = array();
    $user = wp_get_current_user();
    $user_id = $user->ID;
    $um = get_user_meta( $user_id, '', false );
    $user_meta = array();
    if(!empty($um)){
        foreach ($um as $key => $val){
            $user_meta[$key] = $val[0];
        }
    }

    if(!$user_meta['user_email_confirmed']){

        $user_email_verification_code_for_link = random_int(1000000000, 9999999999);
        $user_email_verification_code = random_int(1000, 9999);

        update_user_meta( $user_id, 'user_email_verification_code_for_link', $user_email_verification_code_for_link );
        update_user_meta( $user_id, 'user_email_verification_code', $user_email_verification_code );

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
        send_email($user->user_email, $general_fields['emails']['auth']['sign_up_subject'], $content);

        $to_json['status'] = 'ok';
        $to_json['label'] = __("New code sent...", TEXTDOMAIN);

    }

    echo json_encode($to_json);

    exit;

}

add_action('wp_ajax_profile_resend_verification_code_to_email', 'profile_resend_verification_code_to_email_action');
