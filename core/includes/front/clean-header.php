<?php

if(!defined('ABSPATH')){exit;}

// clean inline gallery CSS
add_filter( 'use_default_gallery_style', '__return_false' );

// clean other CSS and JS on header
if (!is_admin()) {
    function my_init_method(){
        wp_deregister_script( 'l10n' );
    }
    add_action('init', 'my_init_method');
}
function remheadlink(){
    remove_action( 'wp_head', 'feed_links_extra', 3 );
    remove_action( 'wp_head', 'feed_links', 2 );
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'index_rel_link' );
    remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
    remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
    remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
    remove_action( 'wp_head', 'wp_generator');
}
add_action('init', 'remheadlink');
add_filter( 'wpseo_json_ld_output', '__return_false' );
add_action( 'wp_print_styles', 'wps_deregister_styles', 100 );
function wps_deregister_styles() {
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'global-styles' );
    wp_dequeue_style( 'classic-theme-styles' );
}
