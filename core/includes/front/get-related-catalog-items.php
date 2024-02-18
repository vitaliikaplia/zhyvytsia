<?php

if(!defined('ABSPATH')){exit;}

function get_related_catalog_items($id, $terms){

    $term_ids = wp_list_pluck($terms, 'term_id');

    $args = array(
        'post_type' => 'catalog',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'post__not_in' => array($id),
        'meta_key' => 'status',
        'meta_value' => 'in_stock',
        'tax_query' => [
            [
                'taxonomy' => 'catalog_categories',
                'field' => 'term_id',
                'terms' => $term_ids,
            ],
        ],
    );

    return Timber::get_posts( $args );

}
