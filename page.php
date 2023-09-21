<?php

if(!defined('ABSPATH')){exit;}

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['custom_fields'] = cache_fields($post->ID);

Timber::render( array( 'page-' . $post->post_name . '.twig', 'page.twig' ), $context, TIMBER_CACHE_TIME );
