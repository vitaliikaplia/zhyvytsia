<?php

if(!defined('ABSPATH')){exit;}

/** footer js */
if(get_option('inline_scripts_and_styles')){
    function custom_footer_js(){
        if($footer_js = get_transient( 'footer_js' )){
            echo $footer_js;
        } else {
            $js  = '';
            $js .= file_get_contents( TEMPLATE_DIRECTORY_URL . "assets/js/jquery.min.js" );
            $js .= file_get_contents( TEMPLATE_DIRECTORY_URL . "assets/js/plugins.min.js" );
            $js .= file_get_contents( TEMPLATE_DIRECTORY_URL . "assets/js/custom.min.js" );
            $footer_js = '<script>'.$js.'</script>';
            set_transient( 'footer_js', $footer_js, TRANSIENTS_TIME );
            echo $footer_js;
        }
    }
    add_filter('wp_footer', 'custom_footer_js');
}
