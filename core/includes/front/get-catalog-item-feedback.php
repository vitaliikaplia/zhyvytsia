<?php

if(!defined('ABSPATH')){exit;}

function get_catalog_item_feedback($id){

    $args = array(
        'post_type' => 'feedback',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => 'catalog_item_feedback',
        'meta_value' => $id
    );

    return Timber::get_posts( $args );

}
