<?php

if(!defined('ABSPATH')){exit;}

/** favicon for dashboard */
function favicon_for_admin() {
	echo '<link rel="shortcut icon" href="'.TEMPLATE_DIRECTORY_URL.'assets/img/favicon2x.png?'.ASSETS_VERSION.'" />';
}
add_action( 'admin_head', 'favicon_for_admin' );
add_action( 'login_head', 'favicon_for_admin' );
