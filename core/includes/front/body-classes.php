<?php

if(!defined('ABSPATH')){exit;}

function app_body_classes($classes) {

    $classes[] = 'preload';

    if(is_user_logged_in()){
        $classes[] = 'logged-in';
    } else {
        $classes[] = 'logged-out';
    }

    if( is_page_template('page-login.php') || is_page_template('page-sign-up.php') || is_page_template('page-forgot-password.php') ) {
        $classes[] = 'auth';
    }

    return $classes;
}

add_filter('body_class', 'app_body_classes');
