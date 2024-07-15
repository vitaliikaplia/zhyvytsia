<?php

if(!defined('ABSPATH')){exit;}

use LiqPay;

/** функція для обробки платежу через LiqPay */
function prepare_online_payment_liqpay($order_id, $total_price_raw, $ids_arr_unique, $ids_arr_count_values, $ids_arr_count, $filtered_ids_arr_count) {

    /** отримуємо налаштування */
    $general_fields = cache_general_fields();

    /** логуємо новий платіж */
    $new_payment_args = array(
        'post_type' => 'payments-log',
        'post_title' => __("Payment for order", TEXTDOMAIN) . ' #' . $order_id,
        'post_content' => '',
        'post_status' => 'publish'
    );
    $payment_id = wp_insert_post($new_payment_args);

    /** готуємо змінні для формування замовлення */
    $total_price_raw = $total_price_raw * 100;
    $validity = 3600 * 24 * 7;
    $paymentType = "debit";

    /** готуємо лінк зворотньої переадресації */
    $arr_for_redirection_link = array(
        'action' => 'redirect_to_new_order',
        'order_id' => $order_id
    );
    $json_for_redirection_link = json_encode($arr_for_redirection_link);
    $encrypted_for_redirection_link = custom_encrypt_decrypt('encrypt', $json_for_redirection_link);
    $redirectUrl = DO_URL . $encrypted_for_redirection_link;

    /** готуємо лінк для веб-хуку (оновлення інформації про оплату) */
    $arr_for_webhook_link = array(
        'action' => 'update_payment_information_liqpay',
        'payment_id' => $payment_id
    );
    $json_for_webhook_link = json_encode($arr_for_webhook_link);
    $encrypted_for_webhook_link = custom_encrypt_decrypt('encrypt', $json_for_webhook_link);
    $webHookUrl = DO_URL . $encrypted_for_webhook_link;

    /** готуємо масив позицій замовлення */
    $args = array(
        'post_type'      => 'catalog',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__in' => $ids_arr_unique,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    $the_query = new WP_Query( $args );
    $items_for_liqpay = array();
    if ( $the_query->have_posts() ) :
        while ( $the_query->have_posts() ) : $the_query->the_post();
            $item = array();
            $item['name'] = get_the_title();
            $qty = $ids_arr_count_values[get_the_ID()];
            $item['qty'] = $qty;
            if( get_field('allow_discount') && $general_fields['shop']['enable_wholesale_discounts'] && $filtered_ids_arr_count >= intval($general_fields['shop']['minimum_quantity_of_products_in_the_cart_for_wholesale_discount']) ){
                $price = get_field('price');
                $price = $price * 100;
                $item['sum'] = ( $price / 100 ) * ( 100 - intval($general_fields['shop']['wholesale_discount_percentage']) );
            } else {
                $price = get_field('price');
                $price = $price * 100;
                $item['sum'] = $price;
            }
            $photo = get_field('photo');
            $item['icon'] = $photo['url'];
            $item['unit'] = plural_form_title($qty, array('товар','товари','товарів'));
            $items_for_liqpay[] = $item;
        endwhile;
    endif;
    wp_reset_postdata();

    /** створюємо LiqPay клієнта */
    $liqpay = new LiqPay($general_fields['shop']['liqpay_public_key'], $general_fields['shop']['liqpay_private_key']);

    /** створення платежу */
    $params = array(
        'action'         => 'pay',
        'amount'         => $total_price_raw / 100,
        'currency'       => 'UAH',
        'description'    => 'Оплата за замовлення #'.$order_id,
        'order_id'       => 'zhyvytsia_order_'.$order_id,
        'version'        => '3',
        'sandbox'        => false, // If sandbox mode is enabled
        'server_url'     => $webHookUrl,
        'result_url'     => $redirectUrl
    );

    $form = $liqpay->cnb_form($params);

    /** оновлюємо мета інформацію про створений платіж */
    update_post_meta( $payment_id, 'order_id', $order_id );
    // update_post_meta( $payment_id, 'params', $params );

    return $form;
}
