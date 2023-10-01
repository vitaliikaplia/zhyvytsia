<?php

if(!defined('ABSPATH')){exit;}

/** custom user role */
add_role(
    'client',
    __( 'Client' ),
    array()
);

/** allow editors edit menus */
$role_object = get_role( 'editor' );
$role_object->add_cap( 'edit_theme_options' );

/** disable wp dashboard for clients */
if(is_admin() && current_user_can('client') && empty($_SERVER['HTTP_X_REQUESTED_WITH']) && !strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
    wp_redirect( BLOGINFO_URL );
    exit;
}

/** disable default WordPress login */
add_action('login_head', function() {
    global $pagenow;
    if( 'wp-login.php' == $pagenow && !isset($_REQUEST['usewplogin']) ) {
        wp_redirect(BLOGINFO_URL);
        return;
    }
});

/** custom user meta html */
function extra_user_profile_fields( $user ) {
    echo Timber::compile( 'dashboard/user.twig', array(
        'user' => $user,
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

    update_user_meta( $user_id, 'user_email_confirmed', htmlspecialchars($_POST['user_email_confirmed'], ENT_QUOTES, 'UTF-8') );
    update_user_meta( $user_id, 'user_phone', htmlspecialchars($_POST['user_phone'], ENT_QUOTES, 'UTF-8') );
    update_user_meta( $user_id, 'user_phone_confirmed', htmlspecialchars($_POST['user_phone_confirmed'], ENT_QUOTES, 'UTF-8') );

}

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

/** custom user columns */
function add_user_phone_column($columns) {
    $columns['user_phone'] = 'Phone Number';
    return $columns;
}
add_filter('manage_users_columns', 'add_user_phone_column');

function show_user_phone_data($value, $column_name, $user_id) {
    if ('user_phone' == $column_name) {
        return nice_phone_format(get_user_meta($user_id, 'user_phone', true));
    }
    return $value;
}
add_action('manage_users_custom_column', 'show_user_phone_data', 10, 3);
