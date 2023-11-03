<?php

if(!defined('ABSPATH')){exit;}

use \MonoPay;

function prepare_online_payment($order_id, $total_price_raw, $ids_arr_unique, $ids_arr_count_values, $ids_arr_count){

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
        'action' => 'update_payment_information',
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
    $items_for_mono = array();
    if ( $the_query->have_posts() ) :
        while ( $the_query->have_posts() ) : $the_query->the_post();
            $item = array();
            $item['name'] = get_the_title();
            $qty = $ids_arr_count_values[get_the_ID()];
            $item['qty'] = $qty;
            if( $general_fields['shop']['enable_wholesale_discounts'] && $ids_arr_count >= intval($general_fields['shop']['minimum_quantity_of_products_in_the_cart_for_wholesale_discount']) ){
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
            $items_for_mono[] = $item;
        endwhile;
    endif;
    wp_reset_postdata();

    /** створили клієнта - через нього будуть слатись запити */
    $monoClient = new \MonoPay\Client($general_fields['shop']['monobank_token']);

    /** створюємо цей об'єкт для платежів */
    $monoPayment = new \MonoPay\Payment($monoClient);

    /** створення платежу */
    $invoice = $monoPayment->create(
        $total_price_raw,
        [
            // деталі оплати
            'merchantPaymInfo' => [
                'reference' => 'zhyvytsia_order_'.$order_id, // номер чека, замовлення, тощо; визначається мерчантом (вами)
                'destination' => 'Оплата за замовлення #'.$order_id, // призначення платежу
                'basketOrder' => $items_for_mono,
            ],
            'redirectUrl' => $redirectUrl, // адреса для повернення (GET) - на цю адресу буде переадресовано користувача після завершення оплати (у разі успіху або помилки)
            'webHookUrl' => $webHookUrl, // адреса для CallBack (POST) – на цю адресу буде надіслано дані про стан платежу при кожній зміні статусу. Зміст тіла запиту ідентичний відповіді запиту “перевірки статусу рахунку”
            'validity' => $validity, // строк дії в секундах, за замовчуванням рахунок перестає бути дійсним через 24 години
            'paymentType' => $paymentType, // debit | hold. Тип операції. Для значення hold термін складає 9 днів. Якщо через 9 днів холд не буде фіналізовано — він скасовується
        ]
    );

    /** оновлюємо мета інформацію про створений платіж */
    update_post_meta( $payment_id, 'invoiceId', $invoice['invoiceId'] );
    update_post_meta( $payment_id, 'pageUrl', $invoice['pageUrl'] );
    update_post_meta( $payment_id, 'order_id', $order_id );

    return $invoice['pageUrl'];

}
