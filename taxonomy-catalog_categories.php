<?php

if(!defined('ABSPATH')){exit;}

$template = array( 'taxonomy-catalog_categories.php.twig' );

$context = Timber::get_context();

$term = get_queried_object();
$context['title'] = $term ? $term->name : 'Archive';
$context['description'] = $term ? $term->description : '';

$context['posts'] = Timber::get_posts();

Timber::render( $template, $context, TIMBER_CACHE_TIME );
