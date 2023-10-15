<?php

if(!defined('ABSPATH')){exit;}

function profile_enter_code_from_email_action(){

    $to_json = array();
    $general_fields = cache_general_fields();

    if( isset($_POST['value_1']) &&
        isset($_POST['value_2']) &&
        isset($_POST['value_3']) &&
        isset($_POST['value_4']) ){

        $value_1 = trim(stripslashes($_POST['value_1']));
        $value_2 = trim(stripslashes($_POST['value_2']));
        $value_3 = trim(stripslashes($_POST['value_3']));
        $value_4 = trim(stripslashes($_POST['value_4']));

        $recover_pin_code = intval( $value_1 . $value_2 . $value_3 . $value_4 );

        $user = wp_get_current_user();
        $user_id = $user->ID;
        $um = get_user_meta( $user_id, '', false );
        $user_meta = array();
        if(!empty($um)){
            foreach ($um as $key => $val){
                $user_meta[$key] = $val[0];
            }
        }

        if($user_meta['user_email_verification_code'] == $recover_pin_code){

            update_user_meta( $user_id, 'user_email_confirmed', true );

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
            send_email($user->user_email, $general_fields['emails']['auth']['user_confirmation_subject'], $content);

            $context = Timber::context();
            $to_json['title'] = __("Email confirmation", TEXTDOMAIN);
            $to_json['content'] = Timber::compile( 'ajax/profile-confirm-email-content-confirmed.twig', $context);
            $to_json['confirmed'] = __('Confirmed', TEXTDOMAIN);

            $to_json['status'] = 'ok';

        } else {
            $to_json['status'] = 'error';
        }

    } else {
        $to_json['status'] = "error";
    }

    echo json_encode($to_json);

    exit;
}

add_action('wp_ajax_profile_enter_code_from_email', 'profile_enter_code_from_email_action');
