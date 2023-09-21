<?php

if(!defined('ABSPATH')){exit;}

function cpt_change_title_text( $title ){
	$screen = get_current_screen();
	if  ( 'post' == $screen->post_type ) {
		$title = __('Enter post name here', TEXTDOMAIN);
	}
	if  ( 'page' == $screen->post_type ) {
		$title = __('Enter page name here', TEXTDOMAIN);
	}
	if  ( 'catalog' == $screen->post_type ) {
		$title = __('Enter catalog item name here', TEXTDOMAIN);
	}
	if  ( 'feedback' == $screen->post_type ) {
		$title = __('Enter first and second name here', TEXTDOMAIN);
	}
	return $title;
}
add_filter( 'enter_title_here', 'cpt_change_title_text' );
