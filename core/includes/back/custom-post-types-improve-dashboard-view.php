<?php

if(!defined('ABSPATH')){exit;}

/** прибираємо елементи швидкого редагування */
function remove_quick_edit_cpt( $actions, $post ) {
    if($post->post_type == "orders-log" || $post->post_type == "payments-log"){
        unset($actions['edit']);
        // unset($actions['trash']);
        unset($actions['view']);
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}
add_filter('post_row_actions','remove_quick_edit_cpt',10,2);

/** прибираємо блок з можливістю оновити пост */
function remove_update_post_widget_cpt() {
    $custom_post_types = array('payments-log','orders-log'); // Add your custom post types here
    global $post_type;
    if(in_array($post_type, $custom_post_types)) {
        remove_meta_box('submitdiv', $post_type, 'side');
    }
}
add_action('do_meta_boxes', 'remove_update_post_widget_cpt');

/** встановлюємо вигляд в 1 колонку */
function set_single_column_layout_cpt($columns) {
    $columns['orders-log'] = 1; // for your custom post type
    $columns['payments-log'] = 1; // for your custom post type
    return $columns;
}
add_filter('screen_layout_columns', 'set_single_column_layout_cpt');
function set_screen_layout_cpt($selected) {
    return 1; // Set the number of columns
}
add_filter('get_user_option_screen_layout_orders-log', 'set_screen_layout_cpt');
add_filter('get_user_option_screen_layout_payments-log', 'set_screen_layout_cpt');
