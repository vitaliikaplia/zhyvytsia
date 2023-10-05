<?php

if(!defined('ABSPATH')){exit;}

function send_email($email, $subject, $content){

    if(get_option('enable_custom_smtp_server')){
        $smtp_from_name =  get_option('smtp_from_name');
        $smtp_username =  get_option('smtp_username');
        $subject = "=?utf-8?B?" . base64_encode( stripslashes($subject) ) . "?=";
    } else {
        $smtp_from_name = BLOGINFO_NAME;
        $smtp_username = get_option('admin_email');
    }

    $headers  = "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "From: " . $smtp_from_name . " <" . $smtp_username . ">\r\n";

    wp_mail($email, $subject, $content, $headers);

}
