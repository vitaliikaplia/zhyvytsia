<?php

if(!defined('ABSPATH')){exit;}

$context = Timber::get_context();
Timber::render( '404.twig', $context, TIMBER_CACHE_TIME );
