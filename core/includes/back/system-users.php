<?php

if(!defined('ABSPATH')){exit;}

/** custom user role */
add_role(
    'client',
    __('Client', TEXTDOMAIN),
    array()
);

//remove_role( 'client' );

/** allow editors to do some */
$role_object = get_role( 'editor' );
$role_object->add_cap( 'edit_theme_options' );
$role_object->add_cap( 'list_users' );
$role_object->add_cap( 'edit_users' );

/** disallow editors to edit admins */
global $pagenow;
if(is_admin() && current_user_can('editor') && $pagenow == 'user-edit.php' && isset($_GET['user_id']) && $_GET['user_id']){

    $user_data = get_userdata( intval($_GET['user_id']) );

    $user_roles = $user_data->roles;

    if (in_array('administrator', $user_roles)) {

        // Hook at the beginning of the user profile edit form.
        add_action('wp_after_admin_bar_render', 'start_ob_capture');
        function start_ob_capture() {
            ob_start('modify_user_profile_fields');
        }

        // Hook at the end of the user profile edit form.
        add_action('admin_footer', 'end_ob_capture');
        function end_ob_capture() {
            ob_end_flush();
        }

        // Callback function to modify the form fields.
        function modify_user_profile_fields($buffer) {
            // Use a DOM parser to manipulate the fields.
            //$dom = new DOMDocument();
            // Suppress errors due to any malformed HTML.
            //@$dom->loadHTML(mb_convert_encoding($buffer, 'HTML-ENTITIES', 'UTF-8'));

            $dom = new DOMDocument();
            // Use internal errors to avoid warnings with HTML5 elements or other DOM issues
            libxml_use_internal_errors(true);
            $dom->loadHTML(mb_convert_encoding($buffer, 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();

            $inputs = $dom->getElementsByTagName('input');
            foreach ($inputs as $input) {
                // Set the fields to 'readonly' and 'disabled'.
                $input->setAttribute('readonly', 'readonly');
                $input->setAttribute('disabled', 'disabled');
            }

            $selects = $dom->getElementsByTagName('select');
            foreach ($selects as $select) {
                // Set the fields to 'readonly' and 'disabled'.
                $select->setAttribute('readonly', 'readonly');
                $select->setAttribute('disabled', 'disabled');
            }

            $textareas = $dom->getElementsByTagName('textarea');
            foreach ($textareas as $textarea) {
                // Set the fields to 'readonly' and 'disabled'.
                $textarea->setAttribute('readonly', 'readonly');
                $textarea->setAttribute('disabled', 'disabled');
            }

            $buttons = $dom->getElementsByTagName('button');
            foreach ($buttons as $button) {
                // Set the fields to 'readonly' and 'disabled'.
                $button->setAttribute('readonly', 'readonly');
                $button->setAttribute('disabled', 'disabled');
            }

            return $dom->saveHTML();
        }

    }
}

/** disable wp dashboard for clients */
if(is_admin() && current_user_can('client') && empty($_SERVER['HTTP_X_REQUESTED_WITH']) && !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
    wp_redirect( BLOGINFO_URL );
    exit;
}

/** disable default WordPress login */
function custom_login_page() {
    global $pagenow;
    if( 'wp-login.php' == $pagenow && !isset($_REQUEST['usewplogin']) ) {
        wp_redirect(BLOGINFO_URL);
        exit;
    }
}
add_action('login_head', 'custom_login_page');

/** custom user meta html */
function extra_user_profile_fields( $user ) {
    echo Timber::compile( 'dashboard/user.twig', array(
        'user' => $user,
        'general_fields' => cache_general_fields(),
        'TEXTDOMAIN' => TEXTDOMAIN,
    ));
}
add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

/** custom user meta save data */
function save_extra_user_profile_fields( $user_id ) {

    if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
        return;
    }

    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    if( current_user_can('administrator') ){

        update_user_meta( $user_id, 'user_email_confirmed', htmlspecialchars($_POST['user_email_confirmed'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'user_phone', fix_phone_format(htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8')));
        update_user_meta( $user_id, 'user_phone_confirmed', htmlspecialchars($_POST['user_phone_confirmed'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'payment_type', htmlspecialchars($_POST['payment_type'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'delivery_type', htmlspecialchars($_POST['delivery_type'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'user_region', htmlspecialchars($_POST['user_region'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'user_city', htmlspecialchars($_POST['user_city'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'user_zip', htmlspecialchars($_POST['user_zip'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'user_address', htmlspecialchars($_POST['user_address'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'user_np_city_ref', htmlspecialchars($_POST['user_np_city_ref'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'user_np_city_name', htmlspecialchars($_POST['user_np_city_name'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'user_np_office_number', htmlspecialchars($_POST['user_np_office_number'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'user_np_office_name', htmlspecialchars($_POST['user_np_office_name'], ENT_QUOTES, 'UTF-8') );
        update_user_meta( $user_id, 'user_pickup_point', htmlspecialchars($_POST['user_pickup_point'], ENT_QUOTES, 'UTF-8') );

    }

}

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

/** custom user columns */
function add_user_phone_column($columns) {
    $columns['user_phone'] = __('Phone Number', TEXTDOMAIN);
    return $columns;
}
add_filter('manage_users_columns', 'add_user_phone_column');
function show_user_phone_data($value, $column_name, $user_id) {
    if ('user_phone' == $column_name) {
        if($user_phone = get_user_meta($user_id, 'user_phone', true)){
            return nice_phone_format($user_phone);
        } else {
            return '-';
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'show_user_phone_data', 10, 3);

/** custom log out */
function custom_logout_redirect( $redirect_to, $requested_redirect_to, $user ) {
    $general_fields = cache_general_fields();
    add_notify('success', __('You have successfully logged out', TEXTDOMAIN));
    return $general_fields['auth']['login']['url'];
}
add_filter( 'logout_redirect', 'custom_logout_redirect', 9999, 3 );
