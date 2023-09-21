<?php

if(!defined('ABSPATH')){exit;}

/** convert to webp function */
function convert_to_webp($image_path, $destination_path, $quality) {
    $imagick = new \Imagick(realpath($image_path));
    $imagick->setImageFormat('webp');
    $imagick->setImageCompressionQuality($quality);
    $imagick->stripImage();
    $imagick->writeImage($destination_path);
    $imagick->clear();
    $imagick->destroy();
}

/** convert to avif function */
//function convert_to_avif($image_path, $destination_path, $quality) {
//    $imagick = new \Imagick(realpath($image_path));
//    $imagick->setImageFormat('avif');
//    $imagick->setImageCompressionQuality($quality);
//    $imagick->stripImage();
//    $imagick->writeImage($destination_path);
//    $imagick->clear();
//    $imagick->destroy();
//}

/** convert old images */
//if(is_admin() && isset($_GET['CONVERT_OLD_IMAGES']) && $_GET['CONVERT_OLD_IMAGES'] && isset($_GET['PER_IMAGE']) && $_GET['PER_IMAGE']){
//
//    $args = array(
//        'post_type' => 'attachment',
//        'post_mime_type' => array('image/jpeg', 'image/jpg', 'image/png', 'image/gif'),
//        'posts_per_page' => intval(stripslashes($_GET['PER_IMAGE'])),
//        'meta_query' => array(
//            'relation' => 'AND',
//            array(
//                'key' => 'webp_path',
//                'compare' => 'NOT EXISTS',
//            ),
//            array(
//                'key' => 'webp_url',
//                'compare' => 'NOT EXISTS',
//            ),
//        ),
//    );
//
//    $attachments = get_posts($args);
//
//    foreach ($attachments as $attachment) {
//        $file = get_attached_file($attachment->ID);
//        $path_parts = pathinfo($file);
//        $webp_path = $path_parts['dirname'] . DS . $path_parts['filename'] . '.webp';
//        if (!file_exists($webp_path)) {
//            convert_to_webp($file, $webp_path, 88);
//        }
//        $origin_url = wp_get_attachment_url($attachment->ID);
//        $webp_url = str_replace('.'.$path_parts['extension'], '.webp', $origin_url);
//        add_post_meta( $attachment->ID, 'webp_path', $webp_path, true );
//        add_post_meta( $attachment->ID, 'webp_url', $webp_url, true );
//    }
//
//}

/** convert new images */
function optimize_images_at_upload($image_data){
    if(in_array($image_data['type'], array(
            'image/gif',
            'image/png',
            'image/jpeg',
            'image/jpg'
        ))){
        $path_parts = pathinfo($image_data['file']);
        $webp_path = $path_parts['dirname'] . DS . $path_parts['filename'] . '.webp';
        $avif_path = $path_parts['dirname'] . DS . $path_parts['filename'] . '.avif';
        convert_to_webp($image_data['file'], $webp_path, 80);
//        convert_to_avif($image_data['file'], $avif_path, 80);
    }
    return $image_data;
}
add_action('wp_handle_upload', 'optimize_images_at_upload');

function add_custom_attachment_meta( $attachment_id ) {
    $attached_file = get_attached_file($attachment_id);
    $path_parts = pathinfo($attached_file);
    $webp_path = $path_parts['dirname'] . DS . $path_parts['filename'] . '.webp';
    $origin_url = wp_get_attachment_url($attachment_id);
    $webp_url = str_replace('.'.$path_parts['extension'], '.webp', $origin_url);
    if (file_exists($webp_path)) {
        add_post_meta( $attachment_id, 'webp_path', $webp_path, true );
        add_post_meta( $attachment_id, 'webp_url', $webp_url, true );
    }
}
add_action( 'add_attachment', 'add_custom_attachment_meta', 100000 );

/** delete webp on original file deletion */
function delete_webp_shadow_image($post_id) {
    $webp_path = get_post_meta($post_id, 'webp_path', true);
    if (file_exists($webp_path)) {
        unlink($webp_path);
    }
}
add_action('delete_attachment', 'delete_webp_shadow_image');
