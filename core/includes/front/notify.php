<?php

if(!defined('ABSPATH')){exit;}

function add_notify($type, $message, $return){
    if($type && $message){
        $notify['type'] = $type;
        $notify['message'] = $message;
        if($return){
            return $notify;
        } else {
            $json = json_encode($notify);
            $encrypted = custom_encrypt_decrypt('encrypt', $json);
            setcookie( 'notify', $encrypted, time() + 1 * DAY_IN_SECONDS, '/' );
        }
    }
}

function render_notify(){
    if(isset($_COOKIE['notify'])){
        $json = custom_encrypt_decrypt('decrypt', $_COOKIE['notify']);
        $data = json_decode($json, true);
        setcookie( 'notify', '', time() - DAY_IN_SECONDS, '/' );
        return $data;
    }
}
