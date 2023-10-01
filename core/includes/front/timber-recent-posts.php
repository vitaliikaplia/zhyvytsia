<?php

if(!defined('ABSPATH')){exit;}

function timber_recent_posts($amount){
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $amount
    );
    return Timber::get_posts( $args );
}
