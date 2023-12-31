<?php

if(!defined('ABSPATH')){exit;}

function get_catalog_item_rating($id){

    $args = array(
        'post_type' => 'feedback',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => 'catalog_item_feedback',
        'meta_value' => $id
    );

    $the_query = new WP_Query( $args );
    if($the_query->found_posts > 0){

        if ( $the_query->have_posts() ) :
            $rate = 0;
            while ( $the_query->have_posts() ) : $the_query->the_post();
                $rate = $rate + get_field('rate', get_the_id());
            endwhile;
        endif;

        wp_reset_postdata();

        $rate = intval($rate) / $the_query->found_posts;
        $label = plural_form(intval($the_query->found_posts), array('відгук','відгука','відгуків'));
    } else {
        $rate = 0;
        $label = 'додати відгук';
    }

    return array(
        'rate' => $rate,
        'items' => $the_query->found_posts,
        'link' => get_the_permalink($id),
        'label' => $label
    );

}
