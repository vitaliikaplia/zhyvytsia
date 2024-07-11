<?php

if(!defined('ABSPATH')){exit;}

/** Custom "do" page */
function custom_system_do_page_callback() {

    /** defining stuff */
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    $path_segments = explode('/', trim($parsed_url['path'], '/'));
    $general_fields = cache_general_fields();
    $profile_url = BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/';
    $login_url = BLOGINFO_URL . '/' . $general_fields['auth']['login']['url'] . '/';
    $password_reset_url = BLOGINFO_URL . '/' . $general_fields['auth']['password_reset']['url'] . '/';

    /** checking 'do' url */
    if (isset($path_segments[0]) && $path_segments[0] === 'do') {

        /** checking suffix url */
        if(!isset($path_segments[1]) && !$path_segments[1]) {
            wp_redirect(BLOGINFO_URL);
            exit;
        }

        /** validating suffix url */
        if(!($decrypted = custom_encrypt_decrypt('decrypt', trim($path_segments[1])))){
            wp_redirect(BLOGINFO_URL);
            exit;
        }

        /** change server response */
        add_action('wp', function(){ status_header( 200 ); });

        /** getting data */
        $arr_data = json_decode($decrypted, true);

        /** do stuff */
        switch ($arr_data['action']) {
            case "verify_email":
                if(
                    ( $user_id = intval(stripslashes($arr_data['user_id'])) )
                    &&
                    ( $verification_code = intval(stripslashes($arr_data['verification_code'])) )
                    &&
                    get_user_meta($user_id, "user_email_verification_code_for_link", true) == $verification_code
                    &&
                    get_user_meta($user_id, "user_email_confirmed", true) != true
                ){
                    update_user_meta( $user_id, 'user_email_confirmed', true );
                    $user_info = get_userdata($user_id);
                    $user_email = $user_info->user_email;
                    $search = array(
                        '[button]',
                        '[session]'
                    );
                    $replace = array(
                        get_email_part('button', array(
                            'link' => BLOGINFO_URL . '/' . $general_fields['profile']['url'] . '/',
                            'title' => __('Go to my profile', TEXTDOMAIN)
                        )),
                        get_session_info(getUserIP())
                    );
                    $content = Timber::compile( 'email/email.twig', array(
                        'TEXTDOMAIN' => TEXTDOMAIN,
                        'BLOGINFO_NAME' => BLOGINFO_NAME,
                        'BLOGINFO_URL' => BLOGINFO_URL,
                        'subject' => $general_fields['emails']['auth']['user_confirmation_subject'],
                        'text' => str_replace($search, $replace, $general_fields['emails']['auth']['user_confirmation_text'])
                    ));
                    send_email($user_email, $general_fields['emails']['auth']['user_confirmation_subject'], $content);
                    add_notify('success', __('Your email verified successfully, thanks!', TEXTDOMAIN));
                    if(is_user_logged_in()){
                        wp_redirect( $profile_url );
                    } else {
                        wp_redirect( $login_url );
                    }
                    break;
                }
            case "password_recovery_request":
                if(
                    ( $user_id = intval(stripslashes($arr_data['user_id'])) )
                    &&
                    ( $password_recovery_code_for_link = intval(stripslashes($arr_data['password_recovery_code_for_link'])) )
                    &&
                    get_user_meta($user_id, "password_recovery_code_for_link", true) == $password_recovery_code_for_link
                    &&
                    isset($arr_data['nonce']) && wp_verify_nonce(sanitize_text_field($arr_data['nonce']), 'reset-password-request')
                ){
                    $user = get_userdata($user_id);
                    $arr_for_json['u_email'] = $user->user_email;
                    $arr_for_json['request_redirect_nonce'] = wp_create_nonce('request-redirect');
                    $arr_for_json['password_recovery_code_for_link'] = $password_recovery_code_for_link;
                    $json = json_encode($arr_for_json);
                    $password_reset_url = $password_reset_url . custom_encrypt_decrypt('encrypt', $json);
                    wp_redirect($password_reset_url);
                } else {
                    add_notify('warning', __('Your link is out of date or invalid', TEXTDOMAIN));
                    if(is_user_logged_in()){
                        wp_redirect( $profile_url );
                    } else {
                        wp_redirect( $login_url );
                    }
                }
                break;
            case "redirect_to_new_order":
                setcookie('checkout-data', '', time() - 3600, '/');
                setcookie('cart', '', time() - 3600, '/', '.'.BLOGINFO_JUST_DOMAIN);
                if( $order_id = intval(stripslashes($arr_data['order_id'])) ){
                    /** preparing new order url */
                    if(is_user_logged_in()){
                        $order_url = $profile_url . 'order/'.$order_id.'/';
                    } else {
                        $order_url = BLOGINFO_URL . '/order/'.$order_id.'/'.get_post_meta($order_id, 'public_order_secret', true).'/';
                    }
                    add_notify('success', $general_fields['shop']['successful_order_message']);
                    wp_redirect($order_url);
                } else {
                    wp_redirect(BLOGINFO_URL);
                }
                break;
            case "update_payment_information_mono":
                if( $payment_id = intval(stripslashes($arr_data['payment_id'])) ){
                    $post_data = file_get_contents("php://input");
                    $data_array = json_decode($post_data, true);

                    if($data_array['invoiceId'] == get_post_meta( $payment_id, 'invoiceId', true )){

                        update_post_meta( $payment_id, 'paymentStatus', $data_array['status'] );
                        update_post_meta( $payment_id, 'payMethod', $data_array['payMethod'] );
                        update_post_meta( $payment_id, 'amount', $data_array['amount'] );
                        update_post_meta( $payment_id, 'ccy', $data_array['ccy'] );
                        update_post_meta( $payment_id, 'finalAmount', $data_array['finalAmount'] );
                        update_post_meta( $payment_id, 'createdDate', $data_array['createdDate'] );
                        update_post_meta( $payment_id, 'modifiedDate', $data_array['modifiedDate'] );
                        update_post_meta( $payment_id, 'reference', $data_array['reference'] );

                        /** sending email to admins */
                        if(!empty($general_fields['shop']['new_order_email_recipients']) && $general_fields['shop']['activate_email_notification_about_payment_status']){

                            $amount_for_email = intval($data_array['amount']) / 100;
                            $amount_for_email = (float)str_replace(",",".",$amount_for_email);
                            $amount_for_email = $amount_for_email . " грн";

                            foreach ($general_fields['shop']['new_order_email_recipients'] as $recipient){

                                /** preparing email content */
                                $search = array(
                                    '[order_id]',
                                    '[payment_status]',
                                    '[amount]',
                                    '[created_date]',
                                    '[modified_date]',
                                    '[button]'
                                );
                                $replace = array(
                                    get_post_meta( $payment_id, 'order_id', true ),
                                    $data_array['status'],
                                    $amount_for_email,
                                    $data_array['createdDate'],
                                    $data_array['modifiedDate'],
                                    get_email_part('button', array(
                                        'link' => BLOGINFO_URL . '/wp-admin/post.php?post='.$payment_id.'&action=edit',
                                        'title' => __('Check payment information', TEXTDOMAIN)
                                    ))
                                );
                                $content = Timber::compile( 'email/email.twig', array(
                                    'TEXTDOMAIN' => TEXTDOMAIN,
                                    'BLOGINFO_NAME' => BLOGINFO_NAME,
                                    'BLOGINFO_URL' => BLOGINFO_URL,
                                    'subject' => str_replace($search, $replace, $general_fields['emails']['checkout']['payment_status_subject_admin']),
                                    'text' => str_replace($search, $replace, $general_fields['emails']['checkout']['payment_status_text_admin'])
                                ));

                                /** sending email */
                                send_email($recipient['email'], str_replace($search, $replace, $general_fields['emails']['checkout']['payment_status_subject_admin']), $content);

                            }
                        }
                    }
                }
                break;
            case "update_payment_information_liqpay":
                if ($payment_id = intval(stripslashes($arr_data['payment_id']))) {
                    $post_data = file_get_contents("php://input");
                    $data_array = array();
                    parse_str($post_data, $data_array);

                    if (isset($data_array['data']) && isset($data_array['signature'])) {
                        // Перевірка підпису
                        $signature = base64_encode(sha1($general_fields['shop']['liqpay_private_key'] . $data_array['data'] . $general_fields['shop']['liqpay_private_key'], true));
                        if ($signature !== $data_array['signature']) {
                            //error_log('Invalid LiqPay signature: ' . $signature);
                            // Невірний підпис
                            exit;
                        }

                        $data_decoded = json_decode(base64_decode($data_array['data']), true);

                        update_post_meta($payment_id, 'payment_id', $data_decoded['payment_id']);
                        update_post_meta($payment_id, 'action', $data_decoded['action']);
                        update_post_meta($payment_id, 'status', $data_decoded['status']);
                        update_post_meta($payment_id, 'version', $data_decoded['version']);
                        update_post_meta($payment_id, 'type', $data_decoded['type']);
                        update_post_meta($payment_id, 'paytype', $data_decoded['paytype']);
                        update_post_meta($payment_id, 'public_key', $data_decoded['public_key']);
                        update_post_meta($payment_id, 'acq_id', $data_decoded['acq_id']);
                        update_post_meta($payment_id, 'order_id', $data_decoded['order_id']);
                        update_post_meta($payment_id, 'liqpay_order_id', $data_decoded['liqpay_order_id']);
                        update_post_meta($payment_id, 'description', $data_decoded['description']);
                        update_post_meta($payment_id, 'sender_phone', $data_decoded['sender_phone']);
                        update_post_meta($payment_id, 'sender_first_name', $data_decoded['sender_first_name']);
                        update_post_meta($payment_id, 'sender_last_name', $data_decoded['sender_last_name']);
                        update_post_meta($payment_id, 'sender_card_mask2', $data_decoded['sender_card_mask2']);
                        update_post_meta($payment_id, 'sender_card_bank', $data_decoded['sender_card_bank']);
                        update_post_meta($payment_id, 'sender_card_type', $data_decoded['sender_card_type']);
                        update_post_meta($payment_id, 'sender_card_country', $data_decoded['sender_card_country']);
                        update_post_meta($payment_id, 'amount', $data_decoded['amount']);
                        update_post_meta($payment_id, 'currency', $data_decoded['currency']);
                        update_post_meta($payment_id, 'sender_commission', $data_decoded['sender_commission']);
                        update_post_meta($payment_id, 'receiver_commission', $data_decoded['receiver_commission']);
                        update_post_meta($payment_id, 'agent_commission', $data_decoded['agent_commission']);
                        update_post_meta($payment_id, 'amount_debit', $data_decoded['amount_debit']);
                        update_post_meta($payment_id, 'amount_credit', $data_decoded['amount_credit']);
                        update_post_meta($payment_id, 'commission_debit', $data_decoded['commission_debit']);
                        update_post_meta($payment_id, 'commission_credit', $data_decoded['commission_credit']);
                        update_post_meta($payment_id, 'currency_debit', $data_decoded['currency_debit']);
                        update_post_meta($payment_id, 'currency_credit', $data_decoded['currency_credit']);
                        update_post_meta($payment_id, 'sender_bonus', $data_decoded['sender_bonus']);
                        update_post_meta($payment_id, 'amount_bonus', $data_decoded['amount_bonus']);
                        update_post_meta($payment_id, 'mpi_eci', $data_decoded['mpi_eci']);
                        update_post_meta($payment_id, 'is_3ds', $data_decoded['is_3ds']);
                        update_post_meta($payment_id, 'language', $data_decoded['language']);
                        update_post_meta($payment_id, 'create_date', $data_decoded['create_date']);
                        update_post_meta($payment_id, 'end_date', $data_decoded['end_date']);
                        update_post_meta($payment_id, 'confirm_phone', $data_decoded['confirm_phone']);
                        update_post_meta($payment_id, 'transaction_id', $data_decoded['transaction_id']);

                        /** sending email to admins */
                        if (!empty($general_fields['shop']['new_order_email_recipients']) && $general_fields['shop']['activate_email_notification_about_payment_status']) {
                            $amount_for_email = intval($data_decoded['amount']) / 100;
                            $amount_for_email = (float)str_replace(",", ".", $amount_for_email);
                            $amount_for_email = $amount_for_email . " грн";

                            foreach ($general_fields['shop']['new_order_email_recipients'] as $recipient) {
                                /** preparing email content */
                                $search = array(
                                    '[order_id]',
                                    '[payment_status]',
                                    '[amount]',
                                    '[created_date]',
                                    '[modified_date]',
                                    '[button]'
                                );
                                $replace = array(
                                    get_post_meta($payment_id, 'order_id', true),
                                    $data_decoded['status'],
                                    $amount_for_email,
                                    date('Y-m-d H:i:s', $data_decoded['create_date']),
                                    date('Y-m-d H:i:s', $data_decoded['end_date']),
                                    get_email_part('button', array(
                                        'link' => BLOGINFO_URL . '/wp-admin/post.php?post=' . $payment_id . '&action=edit',
                                        'title' => __('Check payment information', TEXTDOMAIN)
                                    ))
                                );
                                $content = Timber::compile('email/email.twig', array(
                                    'TEXTDOMAIN' => TEXTDOMAIN,
                                    'BLOGINFO_NAME' => BLOGINFO_NAME,
                                    'BLOGINFO_URL' => BLOGINFO_URL,
                                    'subject' => str_replace($search, $replace, $general_fields['emails']['checkout']['payment_status_subject_admin']),
                                    'text' => str_replace($search, $replace, $general_fields['emails']['checkout']['payment_status_text_admin'])
                                ));

                                /** sending email */
                                send_email($recipient['email'], str_replace($search, $replace, $general_fields['emails']['checkout']['payment_status_subject_admin']), $content);
                            }
                        }
                    } else {
                        //error_log('LiqPay data or signature missing: ' . print_r($data_array, true));
                    }
                }
                break;
            default:
                wp_redirect(BLOGINFO_URL);
                break;
        }

    }

}
add_action( 'init', 'custom_system_do_page_callback' );
