<?php

if(!defined('ABSPATH')){exit;}

/**
 * Count post
 */
function like_this_post_action() {

    if( isset($_POST['currentPageUrl']) && isset($_POST['postId']) ) {
        if(get_the_permalink(intval($_POST['postId'])) == $_POST['currentPageUrl']){
            $post_likes = get_post_meta( intval($_POST['postId']), 'post_likes', true );
            if(isset($_COOKIE['post-id-'.intval($_POST['postId']).'-liked']) && isset($_COOKIE['post-id-'.intval($_POST['postId']).'-liked'])){
                $post_likes = $post_likes - 1;
                if($post_likes < 0){
                    $post_likes = 0;
                }
                $operation = 'minus';
            } else {
                $post_likes = $post_likes + 1;
                $operation = 'plus';
            }
            update_post_meta( intval($_POST['postId']), 'post_likes', $post_likes );
            $toJson['status'] = "ok";
            $toJson['html'] = $post_likes;
            $toJson['post_id'] = intval($_POST['postId']);
            $toJson['operation'] = $operation;
            echo json_encode($toJson);
        }
    }

    exit;
}

add_action( 'wp_ajax_like_this_post', 'like_this_post_action' );
add_action( 'wp_ajax_nopriv_like_this_post', 'like_this_post_action' );
