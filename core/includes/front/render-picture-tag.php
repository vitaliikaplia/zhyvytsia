<?php

if(!defined('ABSPATH')){exit;}

/** html filter to render picture tag from timber image object */
function render_picture_tag($picture){

    if(is_array($picture)){
        $webp_url = get_post_meta($picture['ID'], 'webp_url', true);
        $picture_url = $picture['url'];
        $picture_alt = $picture['alt'];
        $picture_w = $picture['width'];
        $picture_h = $picture['height'];
        $mime_type = $picture['mime_type'];
    } elseif(is_object($picture)){
        $picture_id = $picture->id;
        $picture_w = $picture->width;
        $picture_h = $picture->height;
        $webp_url = get_post_meta($picture_id, 'webp_url', true);
        $picture_url = wp_get_attachment_image_url($picture_id, 'full');
        $picture_alt = get_post_meta($picture_id, '_wp_attachment_image_alt', TRUE);
        $mime_type = get_post_mime_type($picture_id);
    }

    return Timber::compile( 'overall/picture-tag.twig', array(
        'site' => new BlankSite(),
        'webp_url' => $webp_url,
        'picture_url' => $picture_url,
        'picture_w' => $picture_w,
        'picture_h' => $picture_h,
        'mime_type' => $mime_type,
        'picture_alt' => $picture_alt,
        'ext' => pathinfo($picture_url, PATHINFO_EXTENSION)
    ));

}
