<?php

if(!defined('ABSPATH')){exit;}

function send_email($email, $subject, $content){
    $headers  = "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "From: " . get_option('smtp_from_name') . " <" . get_option('smtp_username') . ">\r\n";
    $subject_coded = "=?utf-8?B?" . base64_encode( stripslashes($subject) ) . "?=";
    wp_mail($email, $subject_coded, $content, $headers);
}
