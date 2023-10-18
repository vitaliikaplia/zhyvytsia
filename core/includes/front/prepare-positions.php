<?php

if(!defined('ABSPATH')){exit;}

function prepare_positions(){

    $general_fields = cache_general_fields();

    $ids_arr = array_filter(explode(".", wp_unslash(stripslashes($_COOKIE['cart']))));

    if(!empty($ids_arr)){

        $ids_arr_count_values = array_count_values($ids_arr);
        $ids_arr_count = count($ids_arr);
        $ids_arr_unique = array_unique($ids_arr);

        $total_price = 0;

        foreach($ids_arr as $id) {
            $total_price += intval(get_field('price',$id));
        }

        if( $general_fields['shop']['enable_wholesale_discounts'] && $ids_arr_count >= intval($general_fields['shop']['minimum_quantity_of_products_in_the_cart_for_wholesale_discount']) ){
            $total_price = ( $total_price / 100 ) * ( 100 - intval($general_fields['shop']['wholesale_discount_percentage']) );
            $suffix = " (-".$general_fields['shop']['wholesale_discount_percentage']."%)";
        } else {
            $suffix = "";
        }

        $args = array(
            'post_type'      => 'catalog',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'post__in' => $ids_arr_unique,
            'orderby' => 'title'
        );

        return array(
            'ids_arr'               => $ids_arr,
            'items'                 => Timber::get_posts( $args ),
            'ids_arr_count_values'  => $ids_arr_count_values,
            'ids_arr_count'         => $ids_arr_count,
            'ids_arr_unique'        => $ids_arr_unique,
            'total_price'           => render_total_price($total_price) . $suffix
        );

    }

}
