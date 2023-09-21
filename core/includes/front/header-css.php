<?php

if(!defined('ABSPATH')){exit;}

/** header css */
function custom_header_css(){
    if(get_option('inline_scripts_and_styles')){
        if($header_css = get_transient( 'header_css' )){
            echo $header_css;
        } else {
            $css  = '';
            $css .= file_get_contents( TEMPLATE_DIRECTORY_URL . "assets/css/style.min.css" );
            $css = str_replace('../',TEMPLATE_DIRECTORY_URL . 'assets/', $css);
            $header_css = '<style type="text/css">'.$css.'</style>';
            set_transient( 'header_css', $header_css, TRANSIENTS_TIME );
            echo "\n";
            echo $header_css;
        }
    }
}
add_filter('wp_head', 'custom_header_css');

if(!get_option('inline_scripts_and_styles') && !is_admin() && $GLOBALS['pagenow'] !== 'wp-login.php'){
    function add_style_css_action() {
        wp_register_style('main', TEMPLATE_DIRECTORY_URL . 'assets/css/style.min.css', '', ASSETS_VERSION);
        wp_enqueue_style('main');
    }
    add_action('init', 'add_style_css_action');
}
