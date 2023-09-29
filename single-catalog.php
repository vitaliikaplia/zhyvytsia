<?php

if(!defined('ABSPATH')){exit;}

$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;
$context['fields'] = get_fields();

Timber::render( array( 'single-catalog.twig' ), $context, TIMBER_CACHE_TIME );
