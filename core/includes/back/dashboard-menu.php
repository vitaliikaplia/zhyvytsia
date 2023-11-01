<?php

if(!defined('ABSPATH')){exit;}

/** hide some dashboard pages */
//if(is_admin()){
	//function remove_menus(){
		//remove_menu_page( 'tools.php' );                  //Tools
		//remove_menu_page( 'index.php' );                  //Dashboard
		//remove_menu_page( 'edit.php' );                   //Posts
		//remove_menu_page( 'upload.php' );                 //Media
		//remove_menu_page( 'edit.php?post_type=page' );    //Pages
		//remove_menu_page( 'themes.php' );                 //Appearance
		//remove_menu_page( 'users.php' );                  //Users
        //remove_submenu_page( 'tools.php', 'site-health.php' );
        //remove_menu_page( 'edit-comments.php' );          //Comments
		//remove_menu_page( 'plugins.php' );                //Plugins
		//remove_menu_page( 'options-general.php' );        //Settings
		//remove_submenu_page( 'tools.php', 'site-health.php' );
		//remove_menu_page( 'sitepress-multilingual-cms/menu/languages.php');
	//}
	//add_action( 'admin_menu', 'remove_menus', 999 );
//}

/** add dashboard menu separator */
function add_admin_menu_separator( $position ) {

    global $menu;
    $menu[ $position ] = array(
        0	=>	'',
        1	=>	'read',
        2	=>	'separator' . $position,
        3	=>	'',
        4	=>	'wp-menu-separator'
    );

}
add_action( 'admin_init', 'add_admin_menu_separator' );

function set_admin_menu_separator() {
    do_action( 'admin_init', 25 );
    do_action( 'admin_init', 29 );
    do_action( 'admin_init', 87 );
}
add_action( 'admin_menu', 'set_admin_menu_separator' );

/** rename posts */
function change_post_menu_label() {
    global $menu;
    global $submenu;
    $menu[5][0] = __('Blog', TEXTDOMAIN);
    $submenu['edit.php'][5][0] = __('Blog', TEXTDOMAIN);
    $submenu['edit.php'][10][0] = __('Add new post', TEXTDOMAIN);

    /** put nav menus as separate section in dashboard menu */
    if( current_user_can('editor') ) {
        $menu[29][0] = $submenu['themes.php'][10][0];
        $menu[29][1] = $submenu['themes.php'][10][1];
        $menu[29][2] = $submenu['themes.php'][10][2];
        $menu[29][4] = 'menu-top menu-icon-appearance';
        $menu[29][5] = 'menu-appearance';
        $menu[29][6] = 'dashicons-list-view';
        remove_menu_page( 'themes.php' );
        remove_menu_page( 'tools.php' );
    }
}
add_action( 'admin_menu', 'change_post_menu_label' );
