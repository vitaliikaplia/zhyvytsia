<?php

if(!defined('ABSPATH')){exit;}

/** system pages */
function custom_system_auth_pages_callback() {

    /** defining stuff */
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));
    $general_fields = cache_general_fields();

    /** catching urls */
    if (
        $path_segments[0] == $general_fields['auth']['login']['url']
        ||
        $path_segments[0] == $general_fields['auth']['sign_up']['url']
        ||
        $path_segments[0] == $general_fields['auth']['forgot_password']['url']
        ||
        $path_segments[0] == $general_fields['auth']['password_reset']['url']
        ||
        $path_segments[0] == $general_fields['profile']['url']
        ||
        $path_segments[0] == $general_fields['shop']['checkout_page_url']
        ||
        $path_segments[0] == 'order'
    ) {

        /** authorised redirects */
        if(is_user_logged_in() && ( $path_segments[0] == $general_fields['auth']['login']['url'] || $path_segments[0] == $general_fields['auth']['sign_up']['url'] || $path_segments[0] == $general_fields['auth']['forgot_password']['url'] || $path_segments[0] == $general_fields['auth']['password_reset']['url'] )){
            wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
            exit;
        }
        if(!is_user_logged_in() && $path_segments[0] == $general_fields['profile']['url']){
            wp_redirect( BLOGINFO_URL . '/' . $general_fields['auth']['login']['url'] . '/' );
            exit;
        }

        /** fixing last slash in custom urls */
        if (empty($_GET) && substr($_SERVER['REQUEST_URI'], -1) !== '/') {
            wp_redirect(BLOGINFO_URL . '/' . trim($_SERVER['REQUEST_URI'], '/') . '/');
            exit();
        }

        /** disable default wp 404 */
        add_action('wp', function(){ status_header( 200 ); });

        /** building context */
        $context = Timber::context();

        /** form menu */
        if (
            $path_segments[0] == $general_fields['auth']['login']['url']
            ||
            $path_segments[0] == $general_fields['auth']['sign_up']['url']
            ||
            $path_segments[0] == $general_fields['auth']['forgot_password']['url']
            ||
            $path_segments[0] == $general_fields['auth']['password_reset']['url']
        ){
            $context['links'] = array(
                array(
                    'url' => BLOGINFO_URL . '/' . $general_fields['auth']['login']['url'] . '/',
                    'title' => __('Login', TEXTDOMAIN)
                ),
                array(
                    'url' => BLOGINFO_URL . '/' . $general_fields['auth']['sign_up']['url'] . '/',
                    'title' => __('Sign Up', TEXTDOMAIN)
                ),
                array(
                    'url' => BLOGINFO_URL . '/' . $general_fields['auth']['forgot_password']['url'] . '/',
                    'title' => __('Forgot Password', TEXTDOMAIN)
                ),
                array(
                    'url' => BLOGINFO_URL,
                    'title' => __('Home page', TEXTDOMAIN)
                )
            );
        }

        /** put decoded context */
        if(
            $path_segments[0] == $general_fields['auth']['login']['url'] && isset($path_segments[1]) && $path_segments[1]
            ||
            $path_segments[0] == $general_fields['auth']['sign_up']['url'] && isset($path_segments[1]) && $path_segments[1]
            ||
            $path_segments[0] == $general_fields['auth']['forgot_password']['url'] && isset($path_segments[1]) && $path_segments[1]
            ||
            $path_segments[0] == $general_fields['auth']['password_reset']['url'] && isset($path_segments[1]) && $path_segments[1]
        ) {
            if( $decrypted = custom_encrypt_decrypt('decrypt', trim($path_segments[1])) ){
                $context['decoded'] = json_decode($decrypted, true);
            }
        }

        /** put decoded context as cookie */
        if( $path_segments[0] == $general_fields['shop']['checkout_page_url'] && isset($_COOKIE['checkout-data']) && $_COOKIE['checkout-data'] ) {
            if( $decrypted = custom_encrypt_decrypt('decrypt', trim($_COOKIE['checkout-data'])) ){
                $context['decoded'] = json_decode($decrypted, true);
                setcookie('checkout-data', '', time() - 3600, '/');
            }
        }

        /** login page */
        if($path_segments[0] == $general_fields['auth']['login']['url']) {

            $template = 'auth/login.twig';
            $title = __('Login', TEXTDOMAIN);
            $context['text'] = $general_fields['auth']['login']['text'];
            $context['links'] = array_values(array_filter($context['links'], fn($subArray) => $subArray['title'] !== $title));

        }

        /** sign-up page */
        if ($path_segments[0] == $general_fields['auth']['sign_up']['url']){

            $template = 'auth/sign-up.twig';
            $title = __('Sign Up', TEXTDOMAIN);
            $context['text'] = $general_fields['auth']['sign_up']['text'];
            $context['links'] = array_values(array_filter($context['links'], fn($subArray) => $subArray['title'] !== $title));

        }

        /** forgot-password page */
        if ($path_segments[0] == $general_fields['auth']['forgot_password']['url']){

            $template = 'auth/forgot-password.twig';
            $title = __('Forgot Password', TEXTDOMAIN);
            $context['text'] = $general_fields['auth']['forgot_password']['text'];
            $context['links'] = array_values(array_filter($context['links'], fn($subArray) => $subArray['title'] !== $title));

        }

        /** password-reset page */
        if ($path_segments[0] == $general_fields['auth']['password_reset']['url']){

            /** checking suffix url */
            if (!$path_segments[1]){
                wp_redirect( BLOGINFO_URL );
                exit;
            }

            /** validating suffix url */
            if(!($decrypted = custom_encrypt_decrypt('decrypt', trim($path_segments[1])))){
                wp_redirect(BLOGINFO_URL);
                exit;
            }

            $template = 'auth/password-reset.twig';
            $title = __('Password Reset', TEXTDOMAIN);
            $context['text'] = $general_fields['auth']['password_reset']['text'];
            $context['links'] = array_values(array_filter($context['links'], fn($subArray) => $subArray['title'] !== $title));

        }

        /** profile pages */
        if ($path_segments[0] == $general_fields['profile']['url']){

            /** main */
            if(!isset($path_segments[1])){

                $template = 'profile/orders.twig';
                $title = __('Orders', TEXTDOMAIN);
                $context['current_page'] = 'orders';

                $sArgs = array(
                    'post_type' => 'orders-log',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'meta_key' => 'order_user_id',
                    'meta_value' => get_current_user_id()
                );
                $context['orders'] = Timber::get_posts($sArgs);
                $context['orders_link_prefix'] = BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/order';

            /** order */
            } elseif ( $path_segments[1] == 'order'){

                if (!$path_segments[2]){
                    wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
                    exit;
                }

                $orderId = intval($path_segments[2]);

                if(strlen($orderId) >= 10){
                    wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
                    exit;
                }

                if(get_post_type($orderId) != "orders-log"){
                    wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
                    exit;
                }

                if(get_post_meta($orderId, 'order_user_id', true) != get_current_user_id()){
                    wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
                    exit;
                }

                if ($path_segments[3]){
                    wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
                    exit;
                }

                $template = 'profile/order.twig';
                $title = __('Order', TEXTDOMAIN);
                $context['current_page'] = 'orders';
                $context['order'] = new TimberPost($orderId);

                $args = array(
                    'post_type'      => 'catalog',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'post__in' => get_post_meta($orderId, 'ids_arr_unique', true),
                    'orderby' => 'title',
                    'order' => 'ASC'
                );
                $context['items'] = Timber::get_posts( $args );
                $context['ids_arr_count_values'] = get_post_meta($orderId, 'ids_arr_count_values', true);
                $context['ids_arr_count_values_prices'] = get_post_meta($orderId, 'ids_arr_count_values_prices', true);

            /** edit */
            } elseif ( $path_segments[1] == 'edit'){

                $template = 'profile/edit.twig';
                $title = __('Edit profile', TEXTDOMAIN);
                $context['current_page'] = 'edit';

            /** change-email */
            } elseif ( $path_segments[1] == 'change-email'){

                $template = 'profile/change-email.twig';
                $title = __('Change email', TEXTDOMAIN);
                $context['current_page'] = 'change-email';

            /** change password */
            } elseif ( $path_segments[1] == 'change-password'){

                $template = 'profile/change-password.twig';
                $title = __('Change password', TEXTDOMAIN);
                $context['current_page'] = 'change-password';

            } elseif ($path_segments[1]){
                wp_redirect( BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/' );
                exit;
            }

        }

        /** checkout page */
        if ($path_segments[0] == $general_fields['shop']['checkout_page_url']){

            [
                'ids_arr' => $ids_arr,
                'items' => $items,
                'ids_arr_count_values' => $ids_arr_count_values,
                'ids_arr_count' => $ids_arr_count,
                'ids_arr_unique' => $ids_arr_unique,
                'total_price' => $total_price
            ] = prepare_positions();

            $context['items'] = $items;
            $context['ids_arr_count_values'] = $ids_arr_count_values;
            $context['ids_arr_count'] = $ids_arr_count;
            $context['ids_arr_unique'] = $ids_arr_unique;
            $context['total_price'] = $total_price;

            $template = 'checkout.twig';
            $title = __('Checkout', TEXTDOMAIN);
            $context['current_page'] = 'checkout';

        }

        /** order page */
        if ($path_segments[0] == 'order'){

            if (!$path_segments[1]){
                wp_redirect( BLOGINFO_URL );
                exit;
            }

            $orderId = intval($path_segments[1]);

            if(strlen($orderId) >= 10){
                wp_redirect( BLOGINFO_URL );
                exit;
            }

            if(get_post_type($orderId) != "orders-log"){
                wp_redirect( BLOGINFO_URL );
                exit;
            }

            if (!$path_segments[2]){
                wp_redirect( BLOGINFO_URL );
                exit;
            }

            if ($path_segments[2] != get_post_meta($orderId, 'public_order_secret', true)){
                wp_redirect( BLOGINFO_URL );
                exit;
            }

            $context['order'] = new TimberPost($orderId);
            $args = array(
                'post_type'      => 'catalog',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'post__in' => get_post_meta($orderId, 'ids_arr_unique', true),
                'orderby' => 'title',
                'order' => 'ASC'
            );
            $context['items'] = Timber::get_posts( $args );
            $context['ids_arr_count_values'] = get_post_meta($orderId, 'ids_arr_count_values', true);
            $context['ids_arr_count_values_prices'] = get_post_meta($orderId, 'ids_arr_count_values_prices', true);

            $template = 'order.twig';
            $title = __('Order', TEXTDOMAIN);
        }

        $context['title'] = $title;
        Timber::render( $template, $context );
        exit;
    }

}
add_action( 'init', 'custom_system_auth_pages_callback' );

/** system pages titles */
add_filter( 'document_title_parts', function( $title_parts_array ) {
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));
    $general_fields = cache_general_fields();
    if($path_segments[0] == $general_fields['auth']['login']['url']){
        $title_parts_array['title'] = __('Login', TEXTDOMAIN);
    } elseif($path_segments[0] == $general_fields['auth']['sign_up']['url']){
        $title_parts_array['title'] = __('Sign Up', TEXTDOMAIN);
    } elseif($path_segments[0] == $general_fields['auth']['forgot_password']['url']){
        $title_parts_array['title'] = __('Forgot Password', TEXTDOMAIN);
    } elseif($path_segments[0] == $general_fields['auth']['password_reset']['url']){
        $title_parts_array['title'] = __('Password Reset', TEXTDOMAIN);
    } elseif($path_segments[0] == $general_fields['profile']['url']){
        if(!isset($path_segments[1])){
            $title_parts_array['title'] = __('Orders', TEXTDOMAIN);
        } elseif ( $path_segments[1] == 'edit'){
            $title_parts_array['title'] = __('Edit profile', TEXTDOMAIN);
        } elseif ( $path_segments[1] == 'change-email'){
            $title_parts_array['title'] = __('Change email', TEXTDOMAIN);
        } elseif ( $path_segments[1] == 'change-password'){
            $title_parts_array['title'] = __('Change password', TEXTDOMAIN);
        }
    } elseif($path_segments[0] == $general_fields['shop']['checkout_page_url']){
        $title_parts_array['title'] = __('Checkout page', TEXTDOMAIN);
    } elseif($path_segments[0] == 'order'){
        $title_parts_array['title'] = __('Order', TEXTDOMAIN);
    }
    return $title_parts_array;
});
