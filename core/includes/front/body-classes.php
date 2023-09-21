<?php

if(!defined('ABSPATH')){exit;}

function app_body_classes($classes) {

    $classes[] = 'preload';

    return $classes;
}

add_filter('body_class', 'app_body_classes');
