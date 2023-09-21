<?php

if(!defined('ABSPATH')){exit;}

/** clear cached svg icons */
function clear_svg_cache(){
    if($cached_svg_icons = get_transient( 'cached_svg_icons' )){
        if(!empty($cached_svg_icons)){
            foreach($cached_svg_icons as $name){
                delete_transient( 'svg_icon-' . $name );
            }
        }
        delete_transient( 'cached_svg_icons' );
    }
}

/** clear transients cache */
function my_acf_update_value( $value, $post_id, $field  ) {

    if (strpos($post_id, 'options') !== false) {
        delete_transient( 'general_fields'.LANG_SUFFIX );
    } elseif ($post_id && get_post_type( $post_id ) == "page"){
        delete_transient( 'custom_page_' . $post_id . '_fields'.LANG_SUFFIX );
    }

    delete_transient( 'header_css' );
    delete_transient( 'footer_js' );

	return $value;
}
add_filter('acf/update_value', 'my_acf_update_value', 10, 3);

/** clear menus cache */
function clear_nav_menus_cache(){
    delete_transient( 'general_fields'.LANG_SUFFIX );
}
add_action('wp_update_nav_menu', 'clear_nav_menus_cache');
