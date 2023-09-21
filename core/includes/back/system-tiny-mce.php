<?php

if(!defined('ABSPATH')){exit;}

/** custom editor css styles */
function my_theme_add_editor_styles() {
    add_editor_style( TEMPLATE_DIRECTORY_URL . '/assets/css/tinymce.min.css?' . ASSETS_VERSION );
}
add_action( 'after_setup_theme', 'my_theme_add_editor_styles' );

/** custom editor buttons lines */
function my_mce_buttons( $buttons ) {
    $option = 'bold,italic,bullist,numlist,alignleft,aligncenter,alignright,link,unlink,wp_adv,undo,redo,spellchecker,fullscreen';
    $buttons = explode(',', $option);
    return $buttons;
}
add_filter( 'mce_buttons', 'my_mce_buttons' );

function my_mce_buttons_2( $buttons ) {
    $option = 'outdent,indent,blockquote,removeformat,charmap,forecolor,backcolor,table,code';
    $buttons = explode(',', $option);
    return $buttons;
}
add_filter( 'mce_buttons_2', 'my_mce_buttons_2' );

function my_mce_buttons_3( $buttons ) {
    $option = 'formatselect,styleselect';
    $buttons = explode(',', $option);
    return $buttons;
}
add_filter( 'mce_buttons_3', 'my_mce_buttons_3' );

/** adding custom style formats and fix important mce configs */
function my_mce_before_init_insert_formats( $init_array ) {
    $style_formats = array(
        array(
            'title' => 'Bold green text',
            'inline' => 'span',
            'classes' => 'boldGreenText'
        ),
        array(
            'title' => 'Title icon style', // Title to show in dropdown
            'selector' => 'img', // Element to add class to
            'classes' => 'titleIcon'
        ),
        array(
            'title' => '50% width column', // Title to show in dropdown
            'block' => 'div',
            'classes' => 'column50',
            'wrapper' => true
        ),
        array(
            'title' => 'Checklist style', // Title to show in dropdown
            'selector' => 'ul', // Element to add class to
            'classes' => 'checkList' // CSS class to add
        )
    );
    $init_array['wpautop'] = false;
    $init_array['tadv_noautop'] = true;
    $init_array['style_formats'] = json_encode( $style_formats );
    return $init_array;
}
add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' );

/** editor plugins */
function mce_register_plugins( $plugin_array ) {
    $plugin_array['table'] = TEMPLATE_DIRECTORY_URL . 'assets/js/mce/table.min.js';
    $plugin_array['code'] = TEMPLATE_DIRECTORY_URL . 'assets/js/mce/code.min.js';
    return $plugin_array;
}
add_filter( 'mce_external_plugins', 'mce_register_plugins' );
