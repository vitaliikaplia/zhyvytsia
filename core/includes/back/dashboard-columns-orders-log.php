<?php

if(!defined('ABSPATH')){exit;}

/** orders columns */
function add_order_columns($cols) {
    $screen = get_current_screen();
    if ($screen && ($screen->post_type == 'orders-log')) {
        $cols['ordered_items'] = __('Ordered items', TEXTDOMAIN);
        $cols['total_amount'] = __('Total amount', TEXTDOMAIN);
        $cols['order_user_first_name'] = __('Order user first name', TEXTDOMAIN);
        $cols['order_user_phone'] = __('Order user phone', TEXTDOMAIN);
        $cols['order_status'] = __('Order status', TEXTDOMAIN);
        $cols = [
            'cb' => $cols['cb'],
            'title' => $cols['title'],
            'ordered_items' => $cols['ordered_items'],
            'total_amount' => $cols['total_amount'],
            'order_user_first_name' => $cols['order_user_first_name'],
            'order_user_phone' => $cols['order_user_phone'],
            'date' => $cols['date'],
            'order_status' => $cols['order_status']
        ];
    }
    return $cols;
}
function add_order_columns_values($column_name, $post_id) {
    if ( 'total_amount' == $column_name ) {
        if ( $total_price = get_post_meta( $post_id, 'total_price', true ) ) {
            echo $total_price;
        } else {
            echo '-';
        }
    }
    if ( 'ordered_items' == $column_name ) {
        if ( $ordered_items = get_post_meta( $post_id, 'ordered_items', true ) ) {
            echo $ordered_items;
        } else {
            echo '-';
        }
    }
    if ( 'order_user_first_name' == $column_name ) {
        if ( $order_user_first_name = get_post_meta( $post_id, 'order_user_first_name', true ) ) {
            echo $order_user_first_name;
        } else {
            echo '-';
        }
    }
    if ( 'order_user_phone' == $column_name ) {
        if ( $order_user_phone = get_post_meta( $post_id, 'order_user_phone', true ) ) {
            echo $order_user_phone;
        } else {
            echo '-';
        }
    }
    if ( 'order_status' == $column_name ) {
        $order_status = get_post_meta( $post_id, 'order_status', true );
        $raw = '<select class="order_status_select" data-post-id="'.$post_id.'">';
        $raw .= '<option></option>';
        foreach(custom_order_statuses() as $key => $value){
            $raw .= '<option value="'.$key.'"';
            if($key == $order_status){
                $raw .= ' selected="selected"';
            }
            $raw .= '>'.$value.'</option>';
        }
        $raw .= '</select>';
        echo $raw;
    }
}
add_filter( 'manage_posts_columns', 'add_order_columns', 1000 );
add_action( 'manage_posts_custom_column', 'add_order_columns_values', 10, 2 );

/**
 * Change order status
 */
function dashboard_change_order_status_action() {

    if( isset($_POST['new_status']) && isset($_POST['post_id']) ){

        $new_status = stripslashes($_POST['new_status']);

        $post_id = stripslashes($_POST['post_id']);

        update_post_meta( $post_id, 'order_status', $new_status );

        $toJson['status'] = "ok";

        echo json_encode($toJson);
    }

    exit;

}
add_action( 'wp_ajax_dashboard_change_order_status', 'dashboard_change_order_status_action' );
