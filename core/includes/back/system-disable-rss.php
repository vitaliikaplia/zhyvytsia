<?php

if(!defined('ABSPATH')){exit;}

remove_action( 'do_feed_rdf',  'do_feed_rdf',  10, 1 );
remove_action( 'do_feed_rss',  'do_feed_rss',  10, 1 );
remove_action( 'do_feed_rss2', 'do_feed_rss2', 10, 1 );
remove_action( 'do_feed_atom', 'do_feed_atom', 10, 1 );

add_action( 'wp', function(){
    remove_action( 'wp_head', 'feed_links_extra', 3 );
    remove_action( 'wp_head', 'feed_links', 2 );
    remove_action( 'wp_head', 'rsd_link' );
});

function redirect_to_home_if_feed() {
    if (is_feed()) {
        wp_redirect( BLOGINFO_URL, 301 );
        exit;
    }
}
add_action('template_redirect', 'redirect_to_home_if_feed');
