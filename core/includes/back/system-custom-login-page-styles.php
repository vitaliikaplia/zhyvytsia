<?php

if(!defined('ABSPATH')){exit;}

/** login page styles */
add_filter( 'login_headerurl', 'login_headerurl_function' );
add_filter( 'login_headertext', 'login_headertext_function' );
add_action( 'login_enqueue_scripts', 'login_enqueue_scripts_function' );

function login_headerurl_function() {
	return BLOGINFO_URL;
}

function login_headertext_function(){
	return BLOGINFO_NAME;
}

function login_enqueue_scripts_function() {
	wp_enqueue_style( 'custom_login_css', TEMPLATE_DIRECTORY_URL . '/assets/css/login.min.css?' . ASSETS_VERSION, false );
}
