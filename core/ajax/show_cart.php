<?php

if(!defined('ABSPATH')){exit;}

/**
 * Show cart
 */
function show_cart_action() {

    [
        'ids_arr' => $ids_arr,
        'items' => $items,
        'ids_arr_count_values' => $ids_arr_count_values,
        'ids_arr_count' => $ids_arr_count,
        'ids_arr_unique' => $ids_arr_unique,
        'total_price' => $total_price
    ] = prepare_positions();

    $general_fields = cache_general_fields();
    $toJson['title'] = __("Cart", TEXTDOMAIN);

    if(empty($ids_arr)){

        $toJson['content'] = $general_fields['shop']['empty_cart_message'];

    } else {

        $context = Timber::context();
        $context['items'] = $items;
        $context['ids_arr_count_values'] = $ids_arr_count_values;
        $context['ids_arr_count'] = $ids_arr_count;
        $context['ids_arr_unique'] = $ids_arr_unique;
        $context['total_price'] = $total_price;
        $context['BLOGINFO_URL'] = BLOGINFO_URL;
        $toJson['content'] = Timber::compile( 'ajax/cart.twig', $context);

    }

	$toJson['status'] = "ok";

	echo json_encode($toJson);
	exit;

}
add_action( 'wp_ajax_show_cart', 'show_cart_action' );
add_action( 'wp_ajax_nopriv_show_cart', 'show_cart_action' );
