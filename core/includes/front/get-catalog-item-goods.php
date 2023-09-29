<?php

if(!defined('ABSPATH')){exit;}

function get_catalog_item_goods($id){

    $args = array(
        'post_type' => 'catalog',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'post__not_in' => array($id),
        'meta_key' => 'status',
        'meta_value' => 'in_stock'
    );

    return Timber::get_posts( $args );

}
