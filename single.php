<?php

if(!defined('ABSPATH')){exit;}

$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;

Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context, TIMBER_CACHE_TIME );
