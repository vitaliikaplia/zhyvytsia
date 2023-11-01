<?php

if(!defined('ABSPATH')){exit;}

function plural_form($number, $after) {
    $cases = array (2, 0, 1, 1, 1, 2);
    return $number.' '.$after[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
}

function plural_form_title($number, $after) {
    $cases = array (2, 0, 1, 1, 1, 2);
    return $after[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
}
