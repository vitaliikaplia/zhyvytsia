<?php

if(!defined('ABSPATH')){exit;}

/**
 * Show cart
 */
function update_checkout_positions_action() {

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

    if(!empty($ids_arr)){

        $context = Timber::context();
        $context['items'] = $items;
        $context['ids_arr_count_values'] = $ids_arr_count_values;
        $context['ids_arr_count'] = $ids_arr_count;
        $context['ids_arr_unique'] = $ids_arr_unique;
        $context['total_price'] = $total_price;
        $toJson['html'] = Timber::compile( 'overall/positions.twig', $context);
        $toJson['status'] = "ok";

    } else {
        $toJson['html'] = $general_fields['shop']['empty_cart_message'];
        $toJson['status'] = "empty";
    }

	echo json_encode($toJson);
	exit;

}
add_action( 'wp_ajax_update_checkout_positions', 'update_checkout_positions_action' );
add_action( 'wp_ajax_nopriv_update_checkout_positions', 'update_checkout_positions_action' );
