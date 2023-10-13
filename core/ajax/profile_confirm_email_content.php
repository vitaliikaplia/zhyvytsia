<?php

if(!defined('ABSPATH')){exit;}

function profile_confirm_email_content_action(){
    $context = Timber::context();
    $to_json['title'] = __("Email confirmation", TEXTDOMAIN);
    $to_json['content'] = Timber::compile( 'profile/profile-confirm-email-content.twig', $context);
    $to_json['status'] = "ok";
    echo json_encode($to_json);
    exit;
}
add_action('wp_ajax_profile_confirm_email_content', 'profile_confirm_email_content_action');
