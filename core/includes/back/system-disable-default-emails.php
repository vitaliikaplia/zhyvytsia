<?php

if(!defined('ABSPATH')){exit;}

add_filter( 'send_password_change_email', '__return_false' );
add_filter( 'send_email_change_email', '__return_false' );

if ( ! function_exists( 'wp_new_user_notification' ) ) :
    function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
        return;
    }
endif;
