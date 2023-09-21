<?php

if(!defined('ABSPATH')){exit;}

/** disable site health email notifications */
add_filter( 'wp_fatal_error_handler_enabled', '__return_false' );
