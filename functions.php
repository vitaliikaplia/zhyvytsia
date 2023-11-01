<?php

if(!defined('ABSPATH')){exit;}

/** vendor */
require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/** let's go! */
require_once 'core' . DIRECTORY_SEPARATOR . 'init.php';

//$photos_raw = file_get_contents(ABSPATH . 'wp-content' . DS . 'photos.txt');

//$dom = new DOMDocument();
//$dom->loadHTML($photos_raw);
//$images = $dom->getElementsByTagName('img');
//$image_srcs = array();
//foreach ($images as $image) {
//    if($image->getAttribute('src') != "/local/templates/main-v2022/img/1.png"){
//        $src = $image->getAttribute('src');
//    } else {
//        $src = $image->getAttribute('data-src');
//    }
//    if (strpos($src, '/geopro-photos/pics/original/060/617/') !== false) {
//        $image_srcs[] = $src;
//        $filename = basename($src);
//        $image_data = file_get_contents($src);
//        file_put_contents(ABSPATH . 'wp-content' . DS . 'ppp' . DS . $filename, $image_data);
//    }
//}

//pr($image_srcs);
//global $_wp_theme_features;
//pr($_wp_theme_features);
// Loop through each support item and display it
//foreach ( $theme_supports as $feature => $value ) {
//    echo '<p>' . $feature . '</p>';
//}


//pr($GLOBALS['wp_filter']);

//function disable_gutenberg_word_block_count() {
//    add_filter( 'block_editor_settings', 'remove_word_block_count', 10, 2 );
//}
//
//function remove_word_block_count( $settings, $post ) {
//    if ( isset( $settings['wordCount']['showWordCount'] ) ) {
//        $settings['wordCount']['showWordCount'] = false;
//    }
//    if ( isset( $settings['__experimentalBlockDirectory']['count'] ) ) {
//        $settings['__experimentalBlockDirectory']['count'] = false;
//    }
//    $settings['richEditingEnabled'] = true;
////    pr($settings);
//    return $settings;
//}
//
//add_action( 'after_setup_theme', 'disable_gutenberg_word_block_count' );

//echo Timber::compile( 'mail/sign-up.twig', array(
//    'TEXTDOMAIN' => TEXTDOMAIN,
//    'BLOGINFO_NAME' => BLOGINFO_NAME,
//    'title' => __("Welcome to Zhyvytsia", TEXTDOMAIN),
//    'preheader' => __("Your verification code", TEXTDOMAIN),
//    'user_email_verification_code' => emoji_numbers(1234567890),
//    'verification_link' => get_page_link_by_page_option_name('profile_page') . "?data="
//));
//exit;

//pr(get_session_info('::1'));
//pr(get_option('admin_email'));
//$auth = get_field('auth', 'options');
//pr($auth['mail_templates']['sign_up_subject']);
//pr(fix_phone_format('0672089900'));

//$general_fields = cache_general_fields();
//use \MonoPay;

//створили клієнта - через нього запити будуть слатись
//$monoClient = new \MonoPay\Client($general_fields['shop']['monobank_token']);

//для створення платежів створюємо цей об'єкт
//$monoPayment = new \MonoPay\Payment($monoClient);

//інформація про платіж
//$invoice = $monoPayment->info('231031E3zcxkL4jwwExZ');
//pr($invoice);

//створення платежу
//$invoice = $monoPayment->create(
//    1000,
//    [
//        //деталі оплати
//        'merchantPaymInfo' => [
//            'reference' => 'my_shop_order_28142', //номер чека, замовлення, тощо; визначається мерчантом (вами)
//            'destination' => 'Оплата за замовлення #28142', //призначення платежу
//            'basketOrder' => [ //наповнення замовлення, використовується для відображення кошика замовлення
//                [
//                    'name' => 'Сосновий квас', //назва товару
//                    'qty' => 4, //кількість
//                    'sum' => 80, //сума у мінімальних одиницях валюти за одиницю товару
//                    'icon' => 'https://zhivitsa.com.ua/wp-content/uploads/2023/07/sosnoviy_text_1-576x1024.jpg', //посилання на зображення товару
//                    'unit' => 'пляшка', //назва одиниці вимiру товару
//                ],
//            ],
//        ],
//        'redirectUrl' => 'https://zhivitsa.com.ua/', //адреса для повернення (GET) - на цю адресу буде переадресовано користувача після завершення оплати (у разі успіху або помилки)
//        'webHookUrl' => 'https://zhivitsa.com.ua/secret234256565453354634/log.php', //адреса для CallBack (POST) – на цю адресу буде надіслано дані про стан платежу при кожній зміні статусу. Зміст тіла запиту ідентичний відповіді запиту “перевірки статусу рахунку”
//        'validity' => 3600 * 24 * 7, //строк дії в секундах, за замовчуванням рахунок перестає бути дійсним через 24 години
//        'paymentType' => 'debit', //debit | hold. Тип операції. Для значення hold термін складає 9 днів. Якщо через 9 днів холд не буде фіналізовано — він скасовується
//    ]
//);

//pr($invoice);

//$general_fields = cache_general_fields();
//
//$np = new NovaPoshtaApi2(
//    $general_fields['shop']['nova_poshta_api_key'],
//    'ua',
//    FALSE,
//    'curl'
//);
//
//$cityData = $np->getCity('c335d9f1-bf5e-11e1-bdef-0026b97ed48a');
//
//pr($cityData);

//
//$warehouses = $np->getWarehouses('69da41bd-3f5d-11de-b509-001d92f78698');
//$results = array();
//
//if($warehouses['success'] && !empty($warehouses['data'])){
//    foreach ($warehouses['data'] as $office){
//        $results[] = array(
//            "id" => $office['Number'],
//            "text" => $office['Description']
//        );
//    }
//}
//
//pr($results);
//$ids_arr = array_filter(explode(".", wp_unslash(stripslashes($_COOKIE['cart']))));
//$general_fields = cache_general_fields();
//if(!empty($ids_arr)){
//    foreach ($ids_arr as $cid){
//        $weight += get_field('weight_in_grams', $cid);
//    }
//    $weight = ceil( ( $weight + intval($general_fields['shop']['reserve_weight_in_grams']) ) / 1000);
//    pr($weight);
//}

//$general_fields = cache_general_fields();
//use LisDev\Delivery\NovaPoshtaApi2;
//$np = new NovaPoshtaApi2(
//    $general_fields['shop']['nova_poshta_api_key'],
//    'ua',
//    FALSE,
//    'curl'
//);
//
//$warehouses = $np->getWarehouses('c335d9f1-bf5e-11e1-bdef-0026b97ed48a');
//
//pr($warehouses);

//$current_user = wp_get_current_user();
//pr($current_user->ID);

//pr(custom_order_statuses());

//$ip = '207.97.227.239';
//pr(get_ip_info($ip));

//[
//    'ids_arr' => $ids_arr,
//    'items' => $items,
//    'ids_arr_count_values' => $ids_arr_count_values,
//    'ids_arr_count' => $ids_arr_count,
//    'ids_arr_unique' => $ids_arr_unique,
//    'total_price' => $total_price,
//    'total_price_raw' => $total_price_raw,
//    'discount' => $discount,
//    'ids_arr_count_values_prices' => $ids_arr_count_values_prices
//] = prepare_positions();
//$args = array(
//    'post_type'      => 'catalog',
//    'post_status'    => 'publish',
//    'posts_per_page' => -1,
//    'post__in' => $ids_arr_unique,
//    'orderby' => 'title',
//    'order' => 'ASC'
//);
//$the_query = new WP_Query( $args );
//$items_for_mono = array();
//if ( $the_query->have_posts() ) :
//    while ( $the_query->have_posts() ) : $the_query->the_post();
//        $item = array();
//        $item['name'] = get_the_title();
//        $qty = $ids_arr_count_values[get_the_ID()];
//        $item['qty'] = $qty;
//        if( $general_fields['shop']['enable_wholesale_discounts'] && $ids_arr_count >= intval($general_fields['shop']['minimum_quantity_of_products_in_the_cart_for_wholesale_discount']) ){
//            $price = get_field('price');
//            $item['sum'] = ( $price / 100 ) * ( 100 - intval($general_fields['shop']['wholesale_discount_percentage']) );
//        } else {
//            $item['sum'] = get_field('price');
//        }
//        $photo = get_field('photo');
//        $item['icon'] = $photo['url'];
//        $item['unit'] = plural_form_title($qty, array('товар','товари','товарів'));
//        $items_for_mono[] = $item;
//    endwhile;
//endif;
//wp_reset_postdata();

//$items_for_mono = [ // наповнення замовлення, використовується для відображення кошика замовлення
//    [
//        'name' => 'Сосновий квас', // назва товару
//        'qty' => 4, // кількість
//        'sum' => 80, // сума у мінімальних одиницях валюти за одиницю товару
//        'icon' => 'https://zhivitsa.com.ua/wp-content/uploads/2023/07/sosnoviy_text_1-576x1024.jpg', // посилання на зображення товару
//        'unit' => 'пляшка', // назва одиниці вимiру товару
//    ],
//    [
//        'name' => 'Сосновий квас', // назва товару
//        'qty' => 4, // кількість
//        'sum' => 80, // сума у мінімальних одиницях валюти за одиницю товару
//        'icon' => 'https://zhivitsa.com.ua/wp-content/uploads/2023/07/sosnoviy_text_1-576x1024.jpg', // посилання на зображення товару
//        'unit' => 'пляшка', // назва одиниці вимiру товару
//    ],
//];

//pr($items_for_mono);

///** логуємо новий платіж */
//$new_payment_args = array(
//    'post_type' => 'payments-log',
//    'post_title' => __("Payment for order", TEXTDOMAIN) . ' #123',
//    'post_content' => '',
//    'post_status' => 'publish'
//);
//$payment_id = wp_insert_post($new_payment_args);
//update_post_meta( $payment_id, 'invoice_id', '2310314vMdocX2bwSk9C' );
//update_post_meta( $payment_id, 'redirect', 'https://pay.mbnk.biz/2310314vMdocX2bwSk9C' );
