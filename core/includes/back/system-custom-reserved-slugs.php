<?php

if(!defined('ABSPATH')){exit;}

/** Reserve Slugs in WordPress */
function custom_reserved_slugs($slug, $post_ID, $post_status, $post_type) {
    $reserved_slugs = array('do');  // Add any other slugs you want to reserve in this array

    if (in_array($slug, $reserved_slugs)) {
        $original_slug = $slug;
        $num = 2;

        // Check if slug exists, and keep incrementing $num until we find a unique slug
        while (get_page_by_path($original_slug . '-' . $num, OBJECT, $post_type)) {
            $num++;
        }

        $slug = $original_slug . '-' . $num;
    }

    return $slug;
}
add_filter('wp_unique_post_slug', 'custom_reserved_slugs', 10, 4);
