<?php

if(!defined('ABSPATH')){exit;}

function show_discount_notify(){

    $ids_arr = array_filter(explode(".", wp_unslash(stripslashes($_COOKIE['cart']))));
    $filtered_ids = array_filter($ids_arr, function($page_id) {
        $allow_discount = get_field('allow_discount', $page_id);
        return $allow_discount === true;
    });
    $ids_arr_count = count($filtered_ids);
//    $difference = intval(get_option('options_shop_minimum_quantity_of_products_in_the_cart_for_wholesale_discount')) - $ids_arr_count;

    return $ids_arr_count < intval(get_option('options_shop_minimum_quantity_of_products_in_the_cart_for_wholesale_discount'));

}
