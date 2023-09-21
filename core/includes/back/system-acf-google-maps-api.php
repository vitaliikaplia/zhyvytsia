<?php

if(!defined('ABSPATH')){exit;}

/** dashboard acf maps google maps api key */
if (function_exists('get_fields') && get_option('google_maps_api_key')) {
    function my_acf_google_map_api( $api ){
        $api['key'] = get_option('google_maps_api_key');
        return $api;
    }
    add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');
}
