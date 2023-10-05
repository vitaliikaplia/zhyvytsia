<?php

if(!defined('ABSPATH')){exit;}

function get_email_part($part, $fields){
    if($part && $fields){
        return Timber::compile( 'email/parts/'.$part.'.twig', array(
            'TEXTDOMAIN' => TEXTDOMAIN,
            'BLOGINFO_NAME' => BLOGINFO_NAME,
            'BLOGINFO_URL' => BLOGINFO_URL,
            'fields' => $fields
        ));
    }
}
