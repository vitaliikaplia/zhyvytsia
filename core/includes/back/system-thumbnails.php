<?php

if(!defined('ABSPATH')){exit;}

/** enable thumbnails */
add_theme_support( 'post-thumbnails' );

/** thumbnails in dashboard area */
if ( !function_exists('AddThumbColumn') && function_exists('add_theme_support') ) {
	// for post and page
    // add_theme_support('post-thumbnails', array( 'post', 'page' ) );
	function AddThumbColumn($cols) {
        $screen = get_current_screen();
        if ($screen && ($screen->post_type == 'post' || $screen->post_type == 'page')) {
            $cols['thumbnail'] = __('Thumbnail', TEXTDOMAIN);
        }
		return $cols;
	}
	function AddThumbValue($column_name, $post_id) {
		$width = (int) 80;
		$height = (int) 80;
		if ( 'thumbnail' == $column_name ) {
			// thumbnail of WP 2.9
			$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
			if ($thumbnail_id)
				$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
			if ( isset($thumb) && $thumb ) {
				echo $thumb;
			} else {
				echo __('None', TEXTDOMAIN);
			}
		}
	}
	// for posts
	add_filter( 'manage_posts_columns', 'AddThumbColumn' );
	add_action( 'manage_posts_custom_column', 'AddThumbValue', 10, 2 );
	// for pages
	add_filter( 'manage_pages_columns', 'AddThumbColumn' );
	add_action( 'manage_pages_custom_column', 'AddThumbValue', 10, 2 );
}
