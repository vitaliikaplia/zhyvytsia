<?php

if(!defined('ABSPATH')){exit;}

/** disable embeds from head */
if(get_option('disable_embeds')){
	function disable_embeds_init() {
		// Remove the REST API endpoint.
		remove_action('rest_api_init', 'wp_oembed_register_route');
		// Turn off oEmbed auto discovery.
		// Don't filter oEmbed results.
		remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
		// Remove oEmbed discovery links.
		remove_action('wp_head', 'wp_oembed_add_discovery_links');
		// Remove oEmbed-specific JavaScript from the front-end and back-end.
		remove_action('wp_head', 'wp_oembed_add_host_js');
	}
	add_action('init', 'disable_embeds_init', 9999);
}
