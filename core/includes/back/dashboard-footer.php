<?php

if(!defined('ABSPATH')){exit;}

/** footer dashboard copyright */
add_filter('admin_footer_text', 'remove_footer_admin');
function remove_footer_admin () {
	echo '<a target="_blank" href="'.AUTHOR_URL.'">'.AUTHOR_TITLE.'</a>';
}
function replace_footer_version()
{
	return __('Powered by', TEXTDOMAIN).' <a target="_blank" href="http://wordpress.org/">WordPress</a>';
}
add_filter( 'update_footer', 'replace_footer_version', '1234');
