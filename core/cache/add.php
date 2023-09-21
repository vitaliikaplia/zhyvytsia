<?php

if(!defined('ABSPATH')){exit;}

/** cache svg icons */
function cache_icon_list($name){
    if($cached_svg_icons = get_transient( 'cached_svg_icons' )){
        $icons = $cached_svg_icons;
    } else {
        $icons = array();
    }
    $icons[] = $name;
    set_transient( 'cached_svg_icons', $icons, TRANSIENTS_TIME );
}
function cache_svg_icon($icon_url){
    if(!check_if_404_error($icon_url)){
        $base_name = basename($icon_url);
        $file_name_arr = pathinfo($base_name);
        if($svg = get_transient( 'svg_icon-' . $file_name_arr['filename'] )){
            return $svg;
        } else {
            $svg = file_get_contents($icon_url);
            cache_icon_list($file_name_arr['filename']);
            set_transient( 'svg_icon-' . $file_name_arr['filename'], $svg, TRANSIENTS_TIME );
            return $svg;
        }
    }
}

/** cache general fields */
function cache_general_fields(){
    if (function_exists('get_fields')) {
        if($general_fields = get_transient( 'general_fields'.LANG_SUFFIX )){
            return $general_fields;
        } else {
            $general_fields = get_fields('options');
            set_transient( 'general_fields'.LANG_SUFFIX, $general_fields, TRANSIENTS_TIME );
            return $general_fields;
        }
    } else {
        return false;
    }
}

/** cache page fields */
function cache_fields($post_id){
    if (function_exists('get_fields')) {
        if($post_id){
            if($fields = get_transient( 'custom_page_' . $post_id . '_fields'.LANG_SUFFIX )){
                return $fields;
            } else {
                $fields = get_fields($post_id);
                set_transient( 'custom_page_' . $post_id . '_fields'.LANG_SUFFIX, $fields, TRANSIENTS_TIME );
                return $fields;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}
