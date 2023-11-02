<?php

if(!defined('ABSPATH')){exit;}

function feedback_form_submit_action() {
    if(
        !empty($_POST['review_for_id']) && isset($_POST['review_for_id'])
        &&
        !empty($_POST['rate']) && isset($_POST['rate'])
        &&
        !empty($_POST['review']) && isset($_POST['review'])
    ){

        $general_fields = cache_general_fields();
        $form_fields = array();
        foreach($_POST as $key => $value){
            if($key != 'action'){
                if($value){
                    $form_fields[$key] = wp_unslash(stripslashes($value));
                }
            }
        }

        $review_for_id = intval($form_fields['review_for_id']);
        $post_type = get_post_type($review_for_id);
        if($post_type == "catalog"){

            $current_user = wp_get_current_user();
            $first_name = $current_user->user_firstname;
            $last_name = $current_user->user_lastname;
            $user_phone = get_user_meta( $current_user->ID, 'user_phone', true );
            $user_email_confirmed = get_user_meta( $current_user->ID, 'user_email_confirmed', true );
            $user_phone_confirmed = get_user_meta( $current_user->ID, 'user_phone_confirmed', true );

            if($first_name && $user_phone && $user_email_confirmed && $user_phone_confirmed){

                if($first_name && $last_name){
                    $post_title = $first_name . ' ' . $last_name;
                } else {
                    $post_title = $first_name;
                }

                $args = array(
                    'post_type' => 'feedback',
                    'post_title' => $post_title,
                    'post_content' => '',
                    'post_status' => 'draft'
                );
                $post_id = wp_insert_post( $args );
                update_field("field_650caed1dfce2", $form_fields['rate'], $post_id);
                update_field("field_650baeba87505", '<p>'.$form_fields['review'].'</p>', $post_id);
                update_field("field_650cb90d23296", $review_for_id, $post_id);

                /** sending email to admins */
                if(!empty($general_fields['shop']['new_review_email_recipients'])){
                    foreach ($general_fields['shop']['new_review_email_recipients'] as $recipient){

                        /** preparing email content */
                        $search = array(
                            '[review_id]',
                            '[button]',
                            '[shop_item]',
                            '[session]'
                        );
                        $replace = array(
                            $post_id,
                            get_email_part('button', array(
                                'link' => BLOGINFO_URL . '/wp-admin/post.php?post='.$post_id.'&action=edit',
                                'title' => __('Check new review', TEXTDOMAIN)
                            )),
                            get_the_title($review_for_id),
                            get_session_info(getUserIP())
                        );
                        $content = Timber::compile( 'email/email.twig', array(
                            'TEXTDOMAIN' => TEXTDOMAIN,
                            'BLOGINFO_NAME' => BLOGINFO_NAME,
                            'BLOGINFO_URL' => BLOGINFO_URL,
                            'subject' => $general_fields['emails']['reviews']['new_review_subject_admin'],
                            'text' => str_replace($search, $replace, $general_fields['emails']['reviews']['new_review_text_admin'])
                        ));

                        /** sending email */
                        send_email($recipient['email'], $general_fields['emails']['reviews']['new_review_subject_admin'], $content);

                    }
                }

                $toJson['title'] = $general_fields['shop']['review_send_success_title'];
                $toJson['content'] = $general_fields['shop']['review_send_success_text'];
                $toJson['post_status'] = "success";

            } else {
                $toJson['title'] = $general_fields['shop']['review_send_failure_title'];
                $toJson['content'] = $general_fields['shop']['review_send_failure_text'];
                $toJson['post_status'] = "failure";
            }

            $toJson['status'] = "ok";

        }
    }

    echo json_encode($toJson);
    exit;
}
add_action( 'wp_ajax_feedback_form_submit', 'feedback_form_submit_action' );
