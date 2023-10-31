<?php

if(!defined('ABSPATH')){exit;}

function app_body_classes($classes) {

    $classes[] = 'preload';

    if(is_user_logged_in()){
        $classes[] = 'logged-in';
    } else {
        $classes[] = 'logged-out';
    }

    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));
    $general_fields = cache_general_fields();

    if (
        $path_segments[0] == $general_fields['auth']['login']['url']
        ||
        $path_segments[0] == $general_fields['auth']['sign_up']['url']
        ||
        $path_segments[0] == $general_fields['auth']['forgot_password']['url']
        ||
        $path_segments[0] == $general_fields['auth']['password_reset']['url']
    ) {
        $classes[] = 'auth';
    }
    if ($path_segments[0] == $general_fields['auth']['login']['url']) {
        $classes[] = 'login';
    }
    if ($path_segments[0] == $general_fields['auth']['sign_up']['url']) {
        $classes[] = 'sign-up';
    }
    if ($path_segments[0] == $general_fields['auth']['forgot_password']['url']) {
        $classes[] = 'forgot-password';
    }
    if ($path_segments[0] == $general_fields['auth']['password_reset']['url']) {
        $classes[] = 'password-reset';
    }
    if ($path_segments[0] == $general_fields['profile']['url']) {
        $classes[] = 'profile';
    }
    if ($path_segments[0] == $general_fields['shop']['checkout_page_url']) {
        $classes[] = 'checkout';
    }
    if ($path_segments[0] == 'order') {
        $classes[] = 'order';
    }

    return $classes;
}

add_filter('body_class', 'app_body_classes');
