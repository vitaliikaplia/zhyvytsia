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
    $options = get_field('auth', 'options');
    $profile = get_field('profile', 'options');

    if ($path_segments[0] == $options['login']['url'] || $path_segments[0] == $options['sign_up']['url'] || $path_segments[0] == $options['forgot_password']['url']) {
        $classes[] = 'auth';
    }
    if ($path_segments[0] == $options['sign_up']['url']) {
        $classes[] = 'sign-up';
    }
    if ($path_segments[0] == $profile['url']) {
        $classes[] = 'profile';
    }

    return $classes;
}

add_filter('body_class', 'app_body_classes');
