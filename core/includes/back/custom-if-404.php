<?php

if(!defined('ABSPATH')){exit;}

/** check if 404 */
function check_if_404_error($url){

    $handle = curl_init($url);
    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

    curl_exec($handle);

    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if($httpCode == 404) {
        return true;
    }

    curl_close($handle);

}
