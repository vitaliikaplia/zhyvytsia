<?php

if(!defined('ABSPATH')){exit;}

function custom_form_secret($fields){
    if(!empty($fields)){
        $json = json_encode($fields);
        return custom_encrypt_decrypt('encrypt', $json);
    }
}
