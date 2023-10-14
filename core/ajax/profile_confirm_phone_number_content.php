<?php

if(!defined('ABSPATH')){exit;}

function profile_confirm_phone_number_content_action(){
    $context = Timber::context();
    $to_json['title'] = __("Phone number confirmation", TEXTDOMAIN);
    $to_json['content'] = Timber::compile( 'ajax/profile-confirm-phone-number-content.twig', $context);
    $to_json['status'] = "ok";
    echo json_encode($to_json);
    exit;
}

add_action('wp_ajax_profile_confirm_phone_number_content', 'profile_confirm_phone_number_content_action');
