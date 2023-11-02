<?php

if(!defined('ABSPATH')){exit;}

function custom_post_likes($post){
    $post_likes = get_field('post_likes',$post->ID);
    if(!$post_likes){
        $post_likes = 0;
    }
    return number_format($post_likes, 0, '', '');
}
