<?php

if(!defined('ABSPATH')){exit;}

/** acf */
if (function_exists('get_fields')) {
    if(get_option('hide_acf')){
        add_filter('acf/settings/show_admin', '__return_false');
    }
    add_filter('acf/settings/save_json', 'my_acf_json_save_point');
    function my_acf_json_save_point( $path ) {
        // update path
        $path = THEME_PATH . DS . 'core' . DS . 'acf-json';
        // return
        return $path;
    }
    add_filter('acf/settings/load_json', 'my_acf_json_load_point');
    function my_acf_json_load_point( $paths ) {
        // remove original path (optional)
        unset($paths[0]);
        // append path
        $paths[] = THEME_PATH . DS . 'core' . DS . 'acf-json';
        // return
        return $paths;
    }
    // ACF nav menu field
    require_once CORE_PATH . DS . 'libs' . DS . 'acf-nav-menu-field' . DS . 'nav-menu-v5.php';
    // Options page for ACF
    $sub_page = array(
        'title' => __("Options", TEXTDOMAIN),
        'slug' => 'website-options',
        'capability' => 'edit_posts',
        'position'   => 30,
    );
    acf_add_options_page($sub_page);
    // Main label for ACF options pages
    acf_set_options_page_menu(__("Options", TEXTDOMAIN));
    acf_set_options_page_title( __("Options", TEXTDOMAIN) );

    // ACF dashboard pages
//    acf_add_options_sub_page(array(
//        'page_title'  => __('Options', TEXTDOMAIN),
//        'menu_title'  => __('Options', TEXTDOMAIN),
//        'slug' => 'options',
//        'parent_slug' => 'themes.php',
//    ));
}

/** timber */
require_once CORE_PATH . DS . 'libs' . DS . 'timber' . DS . 'timber.php';
Timber::$dirname = TIMBER_VIEWS;
