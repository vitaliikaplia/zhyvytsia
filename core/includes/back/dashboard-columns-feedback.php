<?php

if(!defined('ABSPATH')){exit;}

/** feedback columns */
function add_feedback_columns($cols) {
    $screen = get_current_screen();
    if ($screen && ($screen->post_type == 'feedback')) {
        $cols['feedback_photo'] = __('Photo', TEXTDOMAIN);
        $cols['feedback_rate'] = __('Rate', TEXTDOMAIN);
        $cols['feedback_catalog_item'] = __('Catalog item', TEXTDOMAIN);
        $cols = [
            'cb' => $cols['cb'],
            'title' => $cols['title'],
            'feedback_catalog_item' => $cols['feedback_catalog_item'],
            'feedback_rate' => $cols['feedback_rate'],
            'author' => $cols['author'],
            'date' => $cols['date'],
            'feedback_photo' => $cols['feedback_photo'],
        ];
    }
    return $cols;
}
function add_feedback_columns_values($column_name, $post_id) {
    if ( 'feedback_photo' == $column_name ) {
        if ( $photo = get_field('photo', $post_id) ) {
            echo '<img src="'.$photo['url'].'" width="100" height="80">';
        } else {
            echo __('None', TEXTDOMAIN);
        }
    }
    if ( 'feedback_rate' == $column_name ) {
        if ( $rate = get_field('rate', $post_id) ) {
            $rating['rate'] = $rate;
            echo Timber::compile( 'overall/rating.twig', array(
                'rating' => $rating,
                'svg_sprite' => SVG_SPRITE_URL
            ));
        } else {
            echo __('None', TEXTDOMAIN);
        }
    }
    if ( 'feedback_catalog_item' == $column_name ) {
        if ( $catalog_item_feedback = get_field('catalog_item_feedback', $post_id) ) {
            echo '<a href="'.get_the_permalink($catalog_item_feedback).'">' . get_the_title($catalog_item_feedback) . '</a>';
        } else {
            echo __('None', TEXTDOMAIN);
        }
    }
}
add_filter( 'manage_posts_columns', 'add_feedback_columns' );
add_action( 'manage_posts_custom_column', 'add_feedback_columns_values', 10, 2 );
//add_filter( 'manage_pages_columns', 'add_feedback_columns' );
//add_action( 'manage_pages_custom_column', 'add_feedback_columns_values', 10, 2 );
