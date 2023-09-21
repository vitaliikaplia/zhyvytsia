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
