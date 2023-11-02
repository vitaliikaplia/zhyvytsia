<?php

if(!defined('ABSPATH')){exit;}

$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;
$context['category'] = get_the_category( $post->ID );

$relatedArgs = array(
    'post_type' => 'post',
    'category__in' => array($context['category'][0]->term_id),
    'post__not_in' => array($context['post']->ID),
    'posts_per_page' => 4
);
$context['related'] = Timber::get_posts( $relatedArgs );

Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context, TIMBER_CACHE_TIME );
