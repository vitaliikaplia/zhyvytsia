<?php

if(!defined('ABSPATH')){exit;}

$static_pages_options = array(
    array(
        "key" => "profile_page",
        "label" => __('Profile page', TEXTDOMAIN)
    ),
);

/**
 * Register and define the settings
 */
add_action('admin_init', 'prfx_admin_init');
function prfx_admin_init(){

    global $static_pages_options;

    foreach($static_pages_options as $option){

        register_setting(
            'reading', // option group "reading", default WP group
            $option['key'], // option name
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => NULL,
            )
        );
        add_settings_field(
            $option['key'], // ID
            $option['label'], // Title
            'static_pages_options_callback', // Callback
            'reading', // page
            'default', // section
            array( 'label_for' => $option['key'], 'option_key' => $option['key'] )
        );

    }

}

/**
 * Custom field callback
 */
function static_pages_options_callback($args) {

    // get saved project page ID
    $page_id = get_option($args['option_key']);

    // get all pages
    $query_args = array(
        'posts_per_page'   => -1,
        'orderby'          => 'name',
        'order'            => 'ASC',
        'post_type'        => 'page',
    );
    $items = get_posts( $query_args );

    echo '<select id="'.$args['option_key'].'" name="'.$args['option_key'].'">';
    // empty option as default
    echo '<option value="0">'.__('Select', TEXTDOMAIN).'</option>';

    // foreach page we create an option element, with the post-ID as value
    foreach($items as $item) {

        // add selected to the option if value is the same as $page_id
        $selected = ($page_id == $item->ID) ? 'selected="selected"' : '';

        echo '<option value="'.$item->ID.'" '.$selected.'>'.$item->post_title.'</option>';
    }

    echo '</select>';

};

/**
 * Add custom state to post/page list
 */
add_filter('display_post_states', 'prfx_add_custom_post_states');
function prfx_add_custom_post_states($states) {
    global $post;
    global $static_pages_options;

    foreach($static_pages_options as $option){

        // get saved project page ID
        $page_id = get_option($option['key']);

        // add our custom state after the post title only,
        // if post-type is "page",
        // "$post->ID" matches the "$page_id",
        // and "$page_id" is not "0"
        if( 'page' == get_post_type($post->ID) && $post->ID == $page_id && $page_id != '0') {
            $states[] = $option['label'];
        }

    }

    return $states;
}
