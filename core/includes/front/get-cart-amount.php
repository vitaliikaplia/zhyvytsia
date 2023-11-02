<?php

if(!defined('ABSPATH')){exit;}

function get_cart_amount(){

    if(isset($_COOKIE['cart']) && $_COOKIE['cart']){
        $ids_arr = array_filter(explode(".", wp_unslash(stripslashes($_COOKIE['cart']))));
        return count($ids_arr);
    }

}
