<?php

if(!defined('ABSPATH')){exit;}

/** Reserve Slugs in WordPress */
function custom_reserved_slugs($slug, $post_ID, $post_status, $post_type) {

    $general_fields = cache_general_fields();
    $reserved_slugs = array('do');
    $reserved_slugs[] = $general_fields['auth']['login']['url'];
    $reserved_slugs[] = $general_fields['auth']['sign_up']['url'];
    $reserved_slugs[] = $general_fields['auth']['forgot_password']['url'];
    $reserved_slugs[] = $general_fields['profile']['url'];
    $reserved_slugs[] = $general_fields['shop']['checkout_page_url'];

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
