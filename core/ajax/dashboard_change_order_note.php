<?php

if(!defined('ABSPATH')){exit;}

function dashboard_change_order_note_action() {

    if( isset($_POST['note']) && isset($_POST['post_id']) ){
        $note = stripslashes($_POST['note']);
        $post_id = stripslashes($_POST['post_id']);
        update_post_meta( $post_id, 'note', $note );
        $toJson['status'] = "ok";
        echo json_encode($toJson);
    }

    exit;

}
add_action( 'wp_ajax_dashboard_change_order_note', 'dashboard_change_order_note_action' );
