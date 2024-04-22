<?php

if(!defined('ABSPATH')){exit;}

/** wp dashboard assets */
function custom_dashboard_assets(){

    /** dashboard custom styles */
    wp_register_style( 'custom-dashboard', TEMPLATE_DIRECTORY_URL . 'assets/css/dashboard.min.css', false, ASSETS_VERSION);
    wp_enqueue_style( 'custom-dashboard' );

    /** options-widget js */
    global $pagenow;
    if($pagenow == "index.php"){
        wp_register_script( 'options-widget', TEMPLATE_DIRECTORY_URL . 'assets/js/options-widget.min.js', '', ASSETS_VERSION, true);
        wp_enqueue_script( 'options-widget' );
    }

    /** orders list js */
    if($pagenow == "edit.php" && isset($_GET['post_type']) && $_GET['post_type'] == "orders-log"){
        wp_register_script( 'select2', TEMPLATE_DIRECTORY_URL . 'assets/js/plugins/select2.min.js', '', ASSETS_VERSION, true);
        wp_enqueue_script( 'select2' );
    }

    /** orders edit assets */
    global $post;
    if( isset( $post->post_type ) ) {
        $current_post_type = $post->post_type;
        if($current_post_type == "orders-log" && $pagenow == "post.php" && isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] == "edit"){
            wp_register_script( 'autogrow-textarea', TEMPLATE_DIRECTORY_URL . 'assets/js/plugins/jquery.autogrow-textarea.js', '', ASSETS_VERSION, true);
            wp_enqueue_script( 'autogrow-textarea' );
            wp_register_script( 'ba-dotimeout', TEMPLATE_DIRECTORY_URL . 'assets/js/plugins/jquery.ba-dotimeout.js', '', ASSETS_VERSION, true);
            wp_enqueue_script( 'ba-dotimeout' );
        }
    }

    /** qrcode */
    wp_register_script( 'qrcode', TEMPLATE_DIRECTORY_URL . 'assets/js/dashboard/qrcode.min.js', '', ASSETS_VERSION, true);
    wp_enqueue_script( 'qrcode' );

    /** rest dashboard js */
    wp_register_script( 'custom-dashboard', TEMPLATE_DIRECTORY_URL . 'assets/js/dashboard.min.js', '', ASSETS_VERSION, true);
    wp_enqueue_script( 'custom-dashboard' );

    /** qrcode */
    wp_register_script( 'qrcode', TEMPLATE_DIRECTORY_URL . 'assets/js/plugins/qrcode.min.js', '', ASSETS_VERSION, true);
    wp_enqueue_script( 'qrcode' );

}
add_action( 'admin_enqueue_scripts', 'custom_dashboard_assets' );
