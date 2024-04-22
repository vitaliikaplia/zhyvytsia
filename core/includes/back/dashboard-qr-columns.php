<?php

if(!defined('ABSPATH')){exit;}

function add_pages_columns($cols) {
    $screen = get_current_screen();
    if ($screen && ($screen->post_type == 'page' || $screen->post_type == 'post' || $screen->post_type == 'catalog')) {
        $cols['qr'] = __('QR', TEXTDOMAIN);
    }
    return $cols;
}
function add_pages_columns_values($column_name, $post_id) {
    if ( 'qr' == $column_name ) {
        if(get_post_status($post_id) == 'publish'){
            $raw  = '<span class="get_qr" style="display: block; width: 96px; height: 96px; cursor: pointer;" data-link="'.get_the_permalink($post_id).'" data-title="'.get_the_title($post_id).'"></span>';
        } else {
            $raw = '-';
        }
        echo $raw;
    }
}
add_filter( 'manage_posts_columns', 'add_pages_columns' );
add_action( 'manage_posts_custom_column', 'add_pages_columns_values', 10, 2 );
add_filter( 'manage_pages_columns', 'add_pages_columns' );
add_action( 'manage_pages_custom_column', 'add_pages_columns_values', 10, 2 );
