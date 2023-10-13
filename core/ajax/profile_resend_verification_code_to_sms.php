<?php

if(!defined('ABSPATH')){exit;}

function profile_resend_verification_code_to_sms_action(){

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

    if(!$user_meta['user_phone_confirmed']){

        $user_sms_verification_code = random_int(1000, 9999);

        update_user_meta( $user_id, 'user_sms_verification_code', $user_sms_verification_code );

        $sms_message = __("Your verification code:", TEXTDOMAIN) . ' ' . emoji_numbers($user_sms_verification_code);

        send_sms(fix_phone_format($user_meta['user_phone']), $sms_message);

        $to_json['status'] = 'ok';
        $to_json['label'] = __("New code sent...", TEXTDOMAIN);

    }

    echo json_encode($to_json);

    exit;

}

add_action('wp_ajax_profile_resend_verification_code_to_sms', 'profile_resend_verification_code_to_sms_action');
