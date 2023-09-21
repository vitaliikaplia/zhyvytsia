<?php

if(!defined('ABSPATH')){exit;}

function change_widget_option_action(){

    if($_POST['label'] && $_POST['type']){

        $label = stripslashes($_POST['label']);
        $type = stripslashes($_POST['type']);
        $val = $_POST['val'];

        $toJson['label'] = $label;
        $toJson['val'] = $val;

        if($type == 'checkbox'){

            $val = filter_var($val, FILTER_VALIDATE_BOOLEAN) ? true : false;

            if($val){
                $toJson['result'] = update_option( $label, $val );
            } else {
                $toJson['result'] = delete_option( $label );
            }

            echo json_encode($toJson);
            exit;

        }

        if($type == 'select-multiple'){

            if(!empty($val)){
                $toJson['result'] = update_option( $label, $val );
            } else {
                $toJson['result'] = delete_option( $label );
            }

            echo json_encode($toJson);
            exit;

        }

        if($type == 'number' || $type == 'text' || $type == 'textarea' || $type == 'password'){
            $val_status = boolval($val) ? true : false;

            if($val_status){
                $toJson['result'] = update_option( $label, $val );
            } else {
                $toJson['result'] = delete_option( $label );
            }

            echo json_encode($toJson);
            exit;
        }
    }

}

add_action( 'wp_ajax_change_widget_option', 'change_widget_option_action' );
