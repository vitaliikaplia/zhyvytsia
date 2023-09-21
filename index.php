<?php

if(!defined('ABSPATH')){exit;}

$context = Timber::get_context();
$context['posts'] = Timber::get_posts();
$templates = array( 'index.twig' );

if ( is_front_page() && is_home() ) {
	//
} elseif( is_front_page() ){
	//
} elseif(is_home()) {
    $context = Timber::get_context();
    $post = new TimberPost();
    $context['post'] = $post;
    array_unshift( $templates, 'blog.twig' );
}
Timber::render( $templates, $context, TIMBER_CACHE_TIME );
