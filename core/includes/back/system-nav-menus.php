<?php

if(!defined('ABSPATH')){exit;}

/** remove <ul> tags from nav menus */
function remove_ul( $menu ){
	return preg_replace( array( '#^<ul[^>]*>#', '#</ul>$#' ), '', $menu );
}
add_filter( 'wp_nav_menu', 'remove_ul' );

/**
 * wp nav menus
 * register_nav_menus( array( 'main_header'=>'main_header' ) );
 * register_nav_menus( array( 'top_header_line_menu'=>'top_header_line_menu' ) );
 */
register_nav_menus();
