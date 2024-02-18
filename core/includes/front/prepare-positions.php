<?php

if(!defined('ABSPATH')){exit;}

function prepare_positions(){

    $general_fields = cache_general_fields();

    $ids_arr = array_filter(explode(".", wp_unslash(stripslashes($_COOKIE['cart']))));

    $filtered_ids = array_filter($ids_arr, function($page_id) {
        $allow_discount = get_field('allow_discount', $page_id);
        return $allow_discount === true;
    });

    if(!empty($ids_arr)){

        $ids_arr_count_values = array_count_values($ids_arr);
        $ids_arr_count = count($ids_arr);
        $filtered_ids_arr_count = count($filtered_ids);
        $ids_arr_unique = array_unique($ids_arr);

        $ids_arr_count_values_prices = array();
        foreach ($ids_arr_count_values as $key => $value){
            if( get_field('allow_discount', $key) && $general_fields['shop']['enable_wholesale_discounts'] && $filtered_ids_arr_count >= intval($general_fields['shop']['minimum_quantity_of_products_in_the_cart_for_wholesale_discount']) ){
                $price = intval(get_field('price',$key))*intval($value);
                $price = ( $price / 100 ) * ( 100 - intval($general_fields['shop']['wholesale_discount_percentage']) );
                $ids_arr_count_values_prices[$key] = $price;
            } else {
                $ids_arr_count_values_prices[$key] = intval(get_field('price',$key))*intval($value);
            }
        }

        $total_price_with_discount = 0;
        $total_price_without_discount = 0;

        foreach($ids_arr as $id) {
            if(get_field('allow_discount', $id)){
                $total_price_with_discount += intval(get_field('price',$id));
            } else {
                $total_price_without_discount += intval(get_field('price',$id));
            }

        }

        if( $general_fields['shop']['enable_wholesale_discounts'] && $filtered_ids_arr_count >= intval($general_fields['shop']['minimum_quantity_of_products_in_the_cart_for_wholesale_discount']) ){
            $total_price_with_discount = ( $total_price_with_discount / 100 ) * ( 100 - intval($general_fields['shop']['wholesale_discount_percentage']) );
            $suffix = " (-".$general_fields['shop']['wholesale_discount_percentage']."%)";
            $discount = $general_fields['shop']['wholesale_discount_percentage'];
        } else {
            $suffix = "";
            $discount = "";
        }

        $total_price = $total_price_with_discount + $total_price_without_discount;

        $args = array(
            'post_type'      => 'catalog',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'post__in' => $ids_arr_unique,
            'orderby' => 'title',
            'order' => 'ASC'
        );

        return array(
            'ids_arr'                       => $ids_arr,
            'items'                         => Timber::get_posts( $args ),
            'ids_arr_count_values'          => $ids_arr_count_values,
            'ids_arr_count'                 => $ids_arr_count,
            'ids_arr_unique'                => $ids_arr_unique,
            'total_price'                   => render_total_price($total_price) . $suffix,
            'total_price_raw'               => $total_price,
            'discount'                      => $discount,
            'ids_arr_count_values_prices'   => $ids_arr_count_values_prices,
            'filtered_ids_arr_count'        => $filtered_ids_arr_count,
        );

    }

}
