<?php

if(!defined('ABSPATH')){exit;}

if(get_option('disable_src_set')){
    // disable srcset on frontend
    function disable_wp_responsive_images() {
        return 1;
    }
    add_filter('max_srcset_image_width', 'disable_wp_responsive_images');
    // disable 768px image generation
    function disable_wp_responsive_image_sizes($sizes) {
        unset($sizes['medium_large']);
        return $sizes;
    }
    add_filter('intermediate_image_sizes_advanced', 'disable_wp_responsive_image_sizes');
}
