<?php

if(!defined('ABSPATH')){exit;}

function add_custom_orders_log_meta_box() {
    add_meta_box(
        'custom-orders-log-fields-meta-box',
        __('Order information', TEXTDOMAIN),
        'orders_log_render_custom_fields',
        'orders-log',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_custom_orders_log_meta_box');

function orders_log_render_custom_fields($post) {
    $context = Timber::context();
    $order = new TimberPost($post);
    $context['order'] = $order;
    if($order_user_id = get_post_meta( $post->ID, 'order_user_id', true )){
        $context['order_user'] = new Timber\User($order_user_id);
    }
    Timber::render( 'dashboard/order-information.twig', $context );
}
