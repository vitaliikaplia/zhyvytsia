<?php

if(!defined('ABSPATH')){exit;}

function add_custom_payments_log_meta_box() {
    add_meta_box(
        'custom-payments-log-fields-meta-box',
        __('Order information', TEXTDOMAIN),
        'payments_log_render_custom_fields',
        'payments-log',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_custom_payments_log_meta_box');

function payments_log_render_custom_fields($post) {
    $context = Timber::context();
    $payment = new TimberPost($post);
    $context['payment'] = $payment;
    Timber::render( 'dashboard/payment-information.twig', $context );
}
