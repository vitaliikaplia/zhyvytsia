<?php

if(!defined('ABSPATH')){exit;}

/** Custom "do" page */
function custom_system_do_page_callback() {

    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));

    if (isset($path_segments[0]) && $path_segments[0] === 'do') {

        if(isset($path_segments[1]) && $path_segments[1]){

            add_action('wp', function(){ status_header( 200 ); });

//             pr(DO_URL);
//             pr($path_segments[1]);

            exit();

        } else {

            wp_redirect(BLOGINFO_URL);
            exit();

        }

    }

}
add_action( 'init', 'custom_system_do_page_callback' );
