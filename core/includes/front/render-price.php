<?php

if(!defined('ABSPATH')){exit;}

/**
 * Render price
 */
function render_price($id, $count, $count_all){

	$general_fields = cache_general_fields();

	$price = intval(get_field('price',$id));

    $ids_arr = array_filter(explode(".", wp_unslash(stripslashes($_COOKIE['cart']))));
    $filtered_ids = array_filter($ids_arr, function($page_id) {
        $allow_discount = get_field('allow_discount', $page_id);
        return $allow_discount === true;
    });
    $ids_arr_count = count($filtered_ids);

    if( get_field('allow_discount', $id) && $general_fields['shop']['enable_wholesale_discounts'] && $ids_arr_count >= intval($general_fields['shop']['minimum_quantity_of_products_in_the_cart_for_wholesale_discount']) ){
        $price = ( $price / 100 ) * ( 100 - intval($general_fields['shop']['wholesale_discount_percentage']) );
    }

    $price = (float)str_replace(",",".",$price) * $count;

    $numbersAfterDot = 0;

	$format_price = number_format($price, $numbersAfterDot, '.', ' '). " грн";

	return '<span>' . $format_price .'</span>';

}

function render_total_price($price){

    $numbersAfterDot = 0;

	$format_price = number_format($price, $numbersAfterDot, '.', ' '). " грн";

	return '<span>'.$format_price.'</span>';

}
