<?php

if(!defined('ABSPATH')){exit;}

function add_custom_mail_log_meta_box() {
    add_meta_box(
        'custom-mail-log-fields-meta-box',
        __('Form fields', TEXTDOMAIN),
        'mail_log_render_custom_fields',
        'mail-log',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_custom_mail_log_meta_box');

function mail_log_render_custom_fields($post) {
    echo get_post_field('post_content', $post->ID);
}
