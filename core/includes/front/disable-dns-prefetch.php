<?php

if(!defined('ABSPATH')){exit;}

/** remove dns-prefetch */
if(get_option('disable_dns_prefetch')){
	remove_action( 'wp_head', 'wp_resource_hints', 2 );
}
