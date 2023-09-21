<?php

if(!defined('ABSPATH')){exit;}

if(get_option('remove_default_image_sizes')){
    function remove_default_image_sizes( $sizes) {
        unset( $sizes['large']); // Added to remove 1024
        unset( $sizes['thumbnail']);
        unset( $sizes['medium']);
        unset( $sizes['medium_large']);
        unset( $sizes['1536x1536']);
        unset( $sizes['2048x2048']);
        return $sizes;
    }
    add_filter('intermediate_image_sizes_advanced', 'remove_default_image_sizes');
}
