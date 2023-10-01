<?php

if(!defined('ABSPATH')){exit;}

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
