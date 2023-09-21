<?php

if(!defined('ABSPATH')){exit;}

/** disable core privacy tools */

if(get_option('disable_core_privacy_tools')){
	function ds_disable_core_privacy_tools( $caps, $cap ) {
		switch ( $cap ) {
			case 'export_others_personal_data':
			case 'erase_others_personal_data':
			case 'manage_privacy_options':
				$caps[] = 'do_not_allow';
				break;
		}
		return $caps;
	}
	add_filter( 'map_meta_cap', 'ds_disable_core_privacy_tools', 10, 2 );
	add_filter( 'pre_option_wp_page_for_privacy_policy', '__return_zero' );
	remove_action( 'init', 'wp_schedule_delete_old_privacy_export_files' );
	remove_action( 'wp_privacy_delete_old_export_files', 'wp_privacy_delete_old_export_files' );
}
