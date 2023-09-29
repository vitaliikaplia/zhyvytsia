<?php

if(!defined('ABSPATH')){exit;}

/** catalog photo column */
function add_catalog_photo_column($cols) {
    $screen = get_current_screen();
    if ($screen && ($screen->post_type == 'catalog')) {
        $cols['catalog_photo'] = __('Photo', TEXTDOMAIN);
        $cols['catalog_price'] = __('Price', TEXTDOMAIN);
        $cols['catalog_rating'] = __('Rating', TEXTDOMAIN);
        $cols['catalog_sku'] = __('SKU', TEXTDOMAIN);
        $cols['catalog_status'] = __('Status', TEXTDOMAIN);
    }
    return $cols;
}
function add_catalog_photo_value($column_name, $post_id) {
    if ( 'catalog_photo' == $column_name ) {
        if ( $photo = get_field('photo', $post_id) ) {
            echo '<img src="'.$photo['url'].'" width="100" height="80">';
        } else {
            echo __('None', TEXTDOMAIN);
        }
    }
    if ( 'catalog_price' == $column_name ) {
        if ( $price = get_field('price', $post_id) ) {
            echo $price . ' грн';
        } else {
            echo __('None', TEXTDOMAIN);
        }
    }
    if ( 'catalog_rating' == $column_name ) {
        $rate = get_catalog_item_rating($post_id);
        echo Timber::compile( 'overall/rating.twig', array(
            'rating' => $rate,
            'svg_sprite' => SVG_SPRITE_URL
        ));
    }
    if ( 'catalog_sku' == $column_name ) {
        if ( $sku = get_field('sku', $post_id) ) {
            echo $sku;
        } else {
            echo __('None', TEXTDOMAIN);
        }
    }
    if ( 'catalog_status' == $column_name ) {
        $field_object = get_field_object('status', $post_id);
        $choices = $field_object['choices'];
        $status = get_field( 'status', $post_id );
        $raw  = '';
        if(is_array($choices)){
            $raw .= '<select class="status_select" data-post-id="'.$post_id.'">';
            foreach($choices as $key => $value){
                if($key == $status['value']){
                    $raw .= '<option value="'.$key.'" selected>'.$value.'</option>';
                } else {
                    $raw .= '<option value="'.$key.'">'.$value.'</option>';
                }
            }
            $raw .= '</select>';
        } else {
            $raw .= __('None', TEXTDOMAIN);
        }
        echo $raw;
    }
}
// for posts
add_filter( 'manage_posts_columns', 'add_catalog_photo_column' );
add_action( 'manage_posts_custom_column', 'add_catalog_photo_value', 10, 2 );
// for pages
add_filter( 'manage_pages_columns', 'add_catalog_photo_column' );
add_action( 'manage_pages_custom_column', 'add_catalog_photo_value', 10, 2 );

/**
 * Change order status
 */
function dashboard_change_order_status_action() {

    if( isset($_POST['new_status']) && isset($_POST['post_id']) ){

        $new_status = stripslashes($_POST['new_status']);
        $post_id = stripslashes($_POST['post_id']);
        update_field("field_650c8cb69cf6d", $new_status, $post_id);

        $toJson['status'] = "ok";

        echo json_encode($toJson);
    }

    exit;

}
add_action( 'wp_ajax_dashboard_change_order_status', 'dashboard_change_order_status_action' );
