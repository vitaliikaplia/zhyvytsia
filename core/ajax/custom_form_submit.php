<?php

if(!defined('ABSPATH')){exit;}

function custom_form_submit_action() {
    if( !empty($_POST['secret']) && isset($_POST['secret']) ){

        $form_options_arr = json_decode(custom_encrypt_decrypt('decrypt', trim(stripslashes($_POST['secret']))), true);
        $form_options = array();
        $form_fields = array();

        foreach($form_options_arr as $value){
            foreach ($value as $key => $value){
                $form_options[$key] = str_replace(PHP_EOL,"<br />",$value);
            }
        }

        foreach($_POST as $key => $value){
            if($key != 'action' && $key != 'secret'){
                if($value){
                    if($key == 'form_name'){
                        $form_name = $value;
                    } elseif ($key == 'form_url'){
                        $form_url = $value;
                    } else {
                        $form_fields[str_replace('_',' ',$key)] = $value;
                    }
                }
            }
        }

        $form_fields['Назва сторінки'] = $form_name;
        $form_fields['URL сторінки'] = $form_url;

        $mail_post = array(
            'post_type' => 'mail-log',
            'post_title' => $form_name . ' (' . implode(", ", array_slice($form_fields, 0, 3)) . ')',
            'post_content' => json_encode($form_fields, JSON_UNESCAPED_UNICODE),
            'post_status' => 'publish'
        );
        wp_insert_post( $mail_post );

        $toJson['form_options'] = $form_options;
        $toJson['form_fields'] = $form_fields;

        $toJson['status'] = "ok";
    }

    echo json_encode($toJson);
    exit;
}
add_action( 'wp_ajax_custom_form_submit', 'custom_form_submit_action' );
add_action( 'wp_ajax_nopriv_custom_form_submit', 'custom_form_submit_action' );
