<?php

if(!defined('ABSPATH')){exit;}

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

function get_platform_info() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $platform = 'Unknown';

    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    return ucfirst($platform);
}

function get_browser_info() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';

    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
    }

    return ucfirst($bname);
}

function get_ip_info($ipAddress = false){

    if($ipAddress){

        if($ipAddress == "::1"){
            return 'Localhost';
        } else {
            // Initialize the reader for country
            $countryReader = new GeoIp2\Database\Reader(CORE_PATH . DS . 'geo' . DS . 'country.mmdb');
            $countryRecord = $countryReader->country($ipAddress);

            // Initialize the reader for city
            $cityReader = new GeoIp2\Database\Reader(CORE_PATH . DS . 'geo' . DS . 'city.mmdb');
            $cityRecord = $cityReader->city($ipAddress);

            return $countryRecord->country->name . ', ' . $cityRecord->mostSpecificSubdivision->name . ', ' . $cityRecord->city->name . ' ('.$ipAddress.')';
        }

    }

}

function get_session_info($ip){
    return get_ip_info($ip) . ', ' . get_platform_info() . ', ' . get_browser_info();
}

