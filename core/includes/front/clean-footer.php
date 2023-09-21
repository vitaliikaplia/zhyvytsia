<?php

if(!defined('ABSPATH')){exit;}

add_action('wp_footer', function () {
    wp_dequeue_style('core-block-supports');
});
