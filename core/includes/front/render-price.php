<?php

if(!defined('ABSPATH')){exit;}

/**
 * Render price
 */
function render_price($id, $count, $count_all){

	$general_fields = cache_general_fields();

	$price = intval(get_field('price',$id));

    if( $general_fields['shop']['enable_wholesale_discounts'] && $count_all >= intval($general_fields['shop']['minimum_quantity_of_products_in_the_cart_for_wholesale_discount']) ){
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
