<?php

if(!defined('ABSPATH')){exit;}

/** [map] */
function short_code_map() {
	return 'map';
}
add_shortcode ('map', 'short_code_map');

/** [year] */
function short_code_year() {
	return date("Y");
}
add_shortcode ('year', 'short_code_year');

/** [b] */
function short_code_b( $atts = array(), $content = null ) {
    $content = "<b>" . do_shortcode( $content ) . "</b>";
    return $content;
}
add_shortcode( 'b', 'short_code_b' );

/** [discount_amount] */
function short_code_discount_amount() {
    return get_option('options_shop_wholesale_discount_percentage');
}
add_shortcode ('discount_amount', 'short_code_discount_amount');

/** [plural_form] */
function short_code_plural_form( $atts ) {
    $attributes = shortcode_atts(
        array(
            'difference' => 0,
            'label' => "товар,товари,товарів",
        ),
        $atts
    );
    if(html_entity_decode($attributes['difference']) == "cookie_cart"){
        $ids_arr = array_filter(explode(".", wp_unslash(stripslashes($_COOKIE['cart']))));
        $filtered_ids = array_filter($ids_arr, function($page_id) {
            $allow_discount = get_field('allow_discount', $page_id);
            return $allow_discount === true;
        });
        $ids_arr_count = count($filtered_ids);
        $difference = intval(get_option('options_shop_minimum_quantity_of_products_in_the_cart_for_wholesale_discount')) - $ids_arr_count;
    } else {
        $difference = intval(html_entity_decode($attributes['difference']));
    }
    $labels = array_map('trim', explode(',', html_entity_decode($attributes['label'])));
    return plural_form((int) $difference, $labels);
}
add_shortcode('plural_form', 'short_code_plural_form');
