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

        $text_content = '';
        $sEmails = array();
        foreach($_POST as $key => $value){
            if($key != 'action' && $key != 'secret'){
                if($value){
                    if($key == 'form_name'){
                        $form_name = $value;
                    } elseif ($key == 'form_url'){
                        $form_url = $value;
                    } else {
                        $form_fields[str_replace('_',' ',$key)] = $value;
                        $text_content .= '<p><b><span>' . str_replace('_',' ',$key) . ':</span></b> ' .  '<span>' . $value . '</span></p>';
                        if($form_options['send_mail_to_sender'] && filter_var($value, FILTER_VALIDATE_EMAIL) && strlen($value) < 60){
                            $sEmails[] = $value;
                        }
                    }
                }
            }
        }

        $text_content .= '<p><b><span>Назва сторінки:</span></b> ' .  '<span>' . $form_name . '</span></p>';
        $text_content .= '<p><b><span>URL сторінки:</span></b> ' .  '<span>' . $form_url . '</span></p>';
        $text_content .= '<p><b><span>Сесія:</span></b> ' .  '<span>' . get_session_info(getUserIP()) . '</span></p>';
        $form_fields['Назва сторінки'] = $form_name;
        $form_fields['URL сторінки'] = $form_url;
        $form_fields['Сесія'] = get_session_info(getUserIP());

        $mail_post = array(
            'post_type' => 'mail-log',
            'post_title' => $form_name . ' (' . implode(", ", array_slice($form_fields, 0, 3)) . ')',
            'post_content' => $text_content,
            'post_status' => 'publish'
        );
        wp_insert_post( $mail_post );

        $emails = get_field('emails', 'options');

        $search = array(
            '[mail]'
        );
        $replace = array(
            $text_content
        );

        $contentToAdmin = Timber::compile( 'email/email.twig', array(
            'TEXTDOMAIN' => TEXTDOMAIN,
            'BLOGINFO_NAME' => BLOGINFO_NAME,
            'BLOGINFO_URL' => BLOGINFO_URL,
            'subject' => $emails['contact']['adm_form_subject'],
            'text' => str_replace($search, $replace, $emails['contact']['adm_form_text'])
        ));

        if(!empty($form_options['email_recipients'])){
            foreach ($form_options['email_recipients'] as $email){
                send_email($email['email'], $emails['contact']['adm_form_subject'], $contentToAdmin);
            }
        }

        if($form_options['send_mail_to_sender'] && !empty($emails)){
            $contentToSender = Timber::compile( 'email/email.twig', array(
                'TEXTDOMAIN' => TEXTDOMAIN,
                'BLOGINFO_NAME' => BLOGINFO_NAME,
                'BLOGINFO_URL' => BLOGINFO_URL,
                'subject' => $emails['contact']['sender_form_subject'],
                'text' => str_replace($search, $replace, $emails['contact']['sender_form_text'])
            ));
            foreach ($sEmails as $sEmail){
                send_email($sEmail, $emails['contact']['sender_form_subject'], $contentToSender);
            }
        }
        $toJson['title'] = $form_options['send_success_title'];
        $toJson['content'] = $form_options['send_success_text'];

        $toJson['status'] = "ok";
    }

    echo json_encode($toJson);
    exit;
}
add_action( 'wp_ajax_custom_form_submit', 'custom_form_submit_action' );
add_action( 'wp_ajax_nopriv_custom_form_submit', 'custom_form_submit_action' );
