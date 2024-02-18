<?php

if(!defined('ABSPATH')){exit;}

/**
 * Catalog
 */
function register_catalog_categories() {
    register_taxonomy( 'catalog_categories',array (
        0 => 'catalog',
    ),
        array(
            'hierarchical' => true,
            'label' => __('Catalog categories', TEXTDOMAIN),
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'catalog-categories', 'with_front' => true ),
            'show_admin_column' => true,
            'labels' => array (
                'search_items' => __('Catalog categories', TEXTDOMAIN),
                'popular_items' => __('Popular', TEXTDOMAIN),
                'all_items' => __('All', TEXTDOMAIN),
                'parent_item' => __('Parent', TEXTDOMAIN),
                'parent_item_colon' => __('Categories', TEXTDOMAIN),
                'edit_item' => __('Edit', TEXTDOMAIN),
                'update_item' => __('Update', TEXTDOMAIN),
                'add_new_item' => __('Add', TEXTDOMAIN),
                'new_item_name' => __('New category', TEXTDOMAIN),
                'separate_items_with_commas' => __('Separate by commas', TEXTDOMAIN),
                'add_or_remove_items' => __('Add or remove', TEXTDOMAIN),
                'choose_from_most_used' => __('Popular', TEXTDOMAIN)
            )
        )
    );
}
add_action('init', 'register_catalog_categories');
