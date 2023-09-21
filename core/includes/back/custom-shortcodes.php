<?php

if(!defined('ABSPATH')){exit;}

/** [map] */
function short_code_map() {
	return 'map';
}
add_shortcode ('map', 'short_code_map');

/** [year] */
function short_code_year() {
	return date("Y");
}
add_shortcode ('year', 'short_code_year');

/** [b] */
function short_code_b( $atts = array(), $content = null ) {
    $content = "<b>" . do_shortcode( $content ) . "</b>";
    return $content;
}
add_shortcode( 'b', 'short_code_b' );
