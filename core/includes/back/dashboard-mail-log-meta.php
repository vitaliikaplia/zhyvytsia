<?php

if(!defined('ABSPATH')){exit;}

function add_custom_mail_log_meta_box() {
    add_meta_box(
        'custom-fields-meta-box',
        __('Form fields', TEXTDOMAIN),
        'mail_log_render_custom_fields',
        'mail-log',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_custom_mail_log_meta_box');

function mail_log_render_custom_fields($post) {
    $content = json_decode(get_post_field('post_content', $post->ID), truue);
    if(!empty($content)){
        echo '<div class="log-data-fields">';
        foreach ($content as $key=>$val){
            echo '<p>';
            echo '<span>'.$key.'</span>';
            echo '<span>'.$val.'</span>';
            echo '</p>';
        }
        echo '</div>';
    }
}
