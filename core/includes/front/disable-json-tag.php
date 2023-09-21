<?php

if(!defined('ABSPATH')){exit;}

/** disable Json from head */
if(get_option('disable_rest_api') && !is_user_logged_in()){

	add_filter( 'rest_enabled', '__return_false' );

	remove_action( 'xmlrpc_rsd_apis',            'rest_output_rsd' );
	remove_action( 'wp_head',                    'rest_output_link_wp_head', 10 );
	remove_action( 'template_redirect',          'rest_output_link_header', 11 );
	remove_action( 'auth_cookie_malformed',      'rest_cookie_collect_status' );
	remove_action( 'auth_cookie_expired',        'rest_cookie_collect_status' );
	remove_action( 'auth_cookie_bad_username',   'rest_cookie_collect_status' );
	remove_action( 'auth_cookie_bad_hash',       'rest_cookie_collect_status' );
	remove_action( 'auth_cookie_valid',          'rest_cookie_collect_status' );
	remove_filter( 'rest_authentication_errors', 'rest_cookie_check_errors', 100 );

	remove_action( 'init',          'rest_api_init' );
	remove_action( 'rest_api_init', 'rest_api_default_filters', 10 );
	remove_action( 'parse_request', 'rest_api_loaded' );

	remove_action( 'rest_api_init',          'wp_oembed_register_route'              );
	remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10 );

	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

    remove_action( 'wp_head', 'wp_oembed_add_host_js' );

}
