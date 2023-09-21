<?php

if(!defined('ABSPATH')){exit;}

/** Hide welcome screen */
add_action('load-index.php', 'hide_welcome_message_panel');
function hide_welcome_message_panel() {
    $user_id = get_current_user_id();
    update_user_meta( $user_id, 'show_welcome_panel', 0 );
}
function my_custom_admin_head_welcome_hide() {
    echo '<style>[for="wp_welcome_panel-hide"] {display: none !important;}</style>';
}
add_action('admin_head', 'my_custom_admin_head_welcome_hide');

/** Hide help tab */
add_action('admin_head', 'remove_help_tabs');
function remove_help_tabs() {
    $screen = get_current_screen();
    $screen->remove_help_tabs();
}
