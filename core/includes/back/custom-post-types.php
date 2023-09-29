<?php

if(!defined('ABSPATH')){exit;}

/**
 * Catalog
 */
register_post_type('catalog', array(
        'label' => __('Catalog', TEXTDOMAIN),
        'description' => '',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => false, 'with_front' => false),
        'query_var' => true,
        'has_archive' => false,
        'menu_position' => 26,
        'menu_icon' => 'dashicons-cart',
        'supports' => array('title','revisions','author'),
        'taxonomies' => array('catalog_categories'),
        'labels' => array (
            'name' => __('Catalog', TEXTDOMAIN),
            'singular_name' => __('Catalog', TEXTDOMAIN),
            'menu_name' => __('Catalog', TEXTDOMAIN),
            'add_new' => __('Add item', TEXTDOMAIN),
            'add_new_item' => __('Add new item', TEXTDOMAIN),
            'edit' => __('Edit', TEXTDOMAIN),
            'edit_item' => __('Edit item', TEXTDOMAIN),
            'new_item' => __('New item', TEXTDOMAIN),
            'view' => __('View', TEXTDOMAIN),
            'view_item' => __('View item', TEXTDOMAIN),
            'search_items' => __('Search for items', TEXTDOMAIN),
            'not_found' => __('No items found', TEXTDOMAIN),
            'not_found_in_trash' => __('No items found in trash', TEXTDOMAIN),
            'parent' => __('Parent item', TEXTDOMAIN)
        )
    )
);

/**
 * Orders
 */
register_post_type('orders-log', array(
        'label' => __('Orders', TEXTDOMAIN),
        'description' => '',
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => false,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => '', 'with_front' => false),
        'query_var' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'menu_position' => 26,
        'menu_icon' => 'dashicons-feedback',
        'supports' => array('title','editor'),
        'capabilities' => array(
            'create_posts' => false
        ),
        'labels' => array (
            'name' => __('Orders', TEXTDOMAIN),
            'singular_name' => __('Order', TEXTDOMAIN),
            'menu_name' => __('Orders', TEXTDOMAIN),
            'add_new' => __('Add', TEXTDOMAIN),
            'add_new_item' => __('Add', TEXTDOMAIN),
            'edit' => __('Edit', TEXTDOMAIN),
            'edit_item' => __('Edit', TEXTDOMAIN),
            'new_item' => __('New', TEXTDOMAIN),
            'view' => __('View', TEXTDOMAIN),
            'view_item' => __('View', TEXTDOMAIN),
            'search_items' => __('Search for orders', TEXTDOMAIN),
            'not_found' => __('No orders found', TEXTDOMAIN),
            'not_found_in_trash' => __('No orders found in trash', TEXTDOMAIN),
            'parent' => __('Parent', TEXTDOMAIN),
        )
    )
);

/**
 * Feedback
 */
register_post_type('feedback', array(
        'label' => __('Feedback', TEXTDOMAIN),
        'description' => '',
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => false, 'with_front' => false),
        'query_var' => true,
        'has_archive' => true,
        'menu_position' => 24,
        'menu_icon' => 'dashicons-format-status',
        'supports' => array('title','revisions','author'),
        'show_in_rest' => false,
        'labels' => array (
            'name' => __('Feedback', TEXTDOMAIN),
            'singular_name' => __('Feedback', TEXTDOMAIN),
            'menu_name' => __('Feedback', TEXTDOMAIN),
            'add_new' => __('Add feedback', TEXTDOMAIN),
            'add_new_item' => __('Add new feedback', TEXTDOMAIN),
            'edit' => __('Edit', TEXTDOMAIN),
            'edit_item' => __('Edit feedback', TEXTDOMAIN),
            'new_item' => __('New feedback', TEXTDOMAIN),
            'view' => __('View', TEXTDOMAIN),
            'view_item' => __('View feedback', TEXTDOMAIN),
            'search_items' => __('Search for feedback', TEXTDOMAIN),
            'not_found' => __('No feedback found', TEXTDOMAIN),
            'not_found_in_trash' => __('No feedback found in trash', TEXTDOMAIN),
            'parent' => __('Parent feedback', TEXTDOMAIN)
        )
    )
);

/**
 * Custom post type for mail logging
 */
register_post_type('mail-log', array(
        'label' => __('Mail log', TEXTDOMAIN),
        'description' => '',
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => false,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => '', 'with_front' => false),
        'query_var' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'menu_position' => 88,
        'menu_icon' => 'dashicons-email-alt',
        'supports' => array('title'),
        'capabilities' => array(
            'create_posts' => false
        ),
        'labels' => array (
            'name' => __('Mail log', TEXTDOMAIN),
            'singular_name' => __('Mail log', TEXTDOMAIN),
            'menu_name' => __('Mail log', TEXTDOMAIN),
            'add_new' => __('Add', TEXTDOMAIN),
            'add_new_item' => __('Add', TEXTDOMAIN),
            'edit' => __('Edit', TEXTDOMAIN),
            'edit_item' => __('Edit', TEXTDOMAIN),
            'new_item' => __('New', TEXTDOMAIN),
            'view' => __('View', TEXTDOMAIN),
            'view_item' => __('View', TEXTDOMAIN),
            'search_items' => __('Search for mail logs', TEXTDOMAIN),
            'not_found' => __('No mail logs found', TEXTDOMAIN),
            'not_found_in_trash' => __('No mail logs found in trash', TEXTDOMAIN),
            'parent' => __('Parent', TEXTDOMAIN),
        )
    )
);
