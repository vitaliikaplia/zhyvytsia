<?php

if(!defined('ABSPATH')){exit;}

function m3u_ts_mime_types($mimes) {
    $mimes['ts'] = 'video/mp2t';
    $mimes['m3u8'] = 'text/plain';
    $mimes['m3u'] = 'text/plain';
    return $mimes;
}
add_filter('upload_mimes', 'm3u_ts_mime_types');
