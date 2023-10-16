<?php

if(!defined('ABSPATH')){exit;}

/** fixing timezone */
date_default_timezone_set( wp_timezone_string() );

/** constants */
define( 'TEXTDOMAIN', 'zhyvytsia' );
define( 'DS', DIRECTORY_SEPARATOR );
define( 'THEME_PATH', trailingslashit( get_template_directory() ) );
define( 'CORE_PATH', THEME_PATH . 'core' );
define( 'TEMPLATE_DIRECTORY_URL', trailingslashit( get_template_directory_uri() ) );
define( 'CORE_URL', TEMPLATE_DIRECTORY_URL . 'core' );
define( 'ADMIN_AJAX_URL', admin_url('admin-ajax.php') );
define( 'BLOGINFO_NAME', get_bloginfo('name') );
define( 'BLOGINFO_URL', get_bloginfo('url') );
define( 'DO_URL', BLOGINFO_URL . '/do/' );
define( 'TIMBER_VIEWS', 'views' );
define( 'IMG_TEMPLATE_DIRECTORY_URL', TEMPLATE_DIRECTORY_URL . 'assets/img' );
define( 'ASSETS_VERSION', get_option('assets_version') );
define( 'SVG_SPRITE_URL', TEMPLATE_DIRECTORY_URL . 'assets/svg/sprite.svg?ver=' . ASSETS_VERSION );
$parsed_url = parse_url(BLOGINFO_URL );
define( 'BLOGINFO_JUST_DOMAIN', $parsed_url['host'] );
define( 'TRANSIENTS_TIME', 48 * HOUR_IN_SECONDS );

/** multilingual constants + wpml */
if( defined('ICL_LANGUAGE_CODE' ) ){
	define( 'BLOGINFO_LANGUAGE', ICL_LANGUAGE_CODE );
	define( 'LANG_SUFFIX', "_" . ICL_LANGUAGE_CODE );
    define( 'ICL_DONT_LOAD_NAVIGATION_CSS', 1 );
    define( 'ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', 1 );
    define( 'ICL_DONT_LOAD_LANGUAGES_JS', 1 );
	define( 'PAGE_ON_FRONT', icl_object_id( get_option('page_on_front'), 'page', false, BLOGINFO_LANGUAGE ) );
	define( 'PAGE_FOR_POSTS', icl_object_id( get_option('page_for_posts'), 'page', false, BLOGINFO_LANGUAGE ) );
} else {
	define( 'BLOGINFO_LANGUAGE', get_locale() );
	define( 'LANG_SUFFIX', "_" . BLOGINFO_LANGUAGE );
	define( 'PAGE_ON_FRONT', get_option('page_on_front') );
	define( 'PAGE_FOR_POSTS', get_option('page_for_posts') );
}

/** template author information */
$currentTheme = wp_get_theme();
define( 'AUTHOR_URL', $currentTheme->get( 'AuthorURI' ) );
define( 'AUTHOR_TITLE', $currentTheme->get( 'Author' ) );

/** load lang files */
load_theme_textdomain( TEXTDOMAIN, CORE_PATH . DS . 'lang' );

/** libraries */
require_once CORE_PATH . DS . 'libs' . DS . 'libraries.php';

/** theme activation */
function activation_function( $oldname, $oldtheme=false ) {
    add_option('assets_version', '0.01');
    add_option('maintenance_mode_title', __('Website under Maintenance', TEXTDOMAIN));
    add_option('maintenance_mode_text', __('We are performing scheduled maintenance. We will be back online shortly.', TEXTDOMAIN));
    add_option('enable_resize_at_upload', true);
    add_option('resize_at_upload_formats', array (
        'image/gif' => 'GIF',
        'image/png' => 'PNG',
        'image/jpeg' => 'JPEG',
        'image/jpg' => 'JPG',
    ));
    add_option('resize_upload_width', 2048);
    add_option('resize_upload_height', 2048);
    add_option('resize_upload_quality', 80);

    add_option('disable_all_updates', true);
    add_option('disable_customizer', true);
    add_option('disable_src_set', true);
    add_option('remove_default_image_sizes', true);
    add_option('disable_core_privacy_tools', true);
    add_option('enable_cyr3lat', true);
    add_option('disable_dns_prefetch', true);
    add_option('disable_emojis', true);
    add_option('disable_embeds', true);
    add_option('hide_dashboard_widgets', true);
    add_option('hide_admin_top_bar', true);
    add_option('disable_admin_email_verification', true);
    add_option('disable_comments', true);
}
add_action('after_switch_theme', 'activation_function', 10, 2);

/** theme deactivation */
function deactivation_function( $newname, $newtheme ) {
    delete_option('assets_version');
    delete_option('maintenance_mode_title');
    delete_option('maintenance_mode_text');
    delete_option('enable_resize_at_upload');
    delete_option('resize_at_upload_formats');
    delete_option('resize_upload_width');
    delete_option('resize_upload_height');
    delete_option('resize_upload_quality');
    delete_option('disable_all_updates');
    delete_option('disable_customizer');
    delete_option('disable_src_set');
    delete_option('remove_default_image_sizes');
    delete_option('disable_core_privacy_tools');
    delete_option('enable_cyr3lat');
    delete_option('disable_dns_prefetch');
    delete_option('disable_emojis');
    delete_option('disable_embeds');
    delete_option('hide_dashboard_widgets');
    delete_option('hide_admin_top_bar');
    delete_option('disable_admin_email_verification');
    delete_option('disable_comments');
}
add_action('switch_theme', 'deactivation_function', 10, 2);

/** load wordpress includes script */
require_once ABSPATH . 'wp-admin' . DS . 'includes' . DS . 'file.php';

/** pr */
require_once CORE_PATH . DS . 'pr.php';

/** cache */
$includedFiles = list_files( CORE_PATH . DS . 'cache' );
if(is_array($includedFiles) && $includedFiles){
	foreach($includedFiles as $file){
		require_once $file;
	}
}

/** custom pages forms logic */
require_once CORE_PATH . DS . 'forms-logic.php';

/** custom do logic */
require_once CORE_PATH . DS . 'do.php';

/** include all modules */
$includedFiles = list_files( CORE_PATH . DS . 'includes' );
if(is_array($includedFiles) && $includedFiles){
	foreach($includedFiles as $file){
		require_once $file;
	}
}

/** gutenberg */
require_once CORE_PATH . DS . 'gutenberg.php';

/** include ajax scripts */
$includedAjax = list_files( CORE_PATH . DS . 'ajax' );
if(is_array($includedAjax) && $includedAjax){
	foreach($includedAjax as $ajax){
		require_once $ajax;
	}
}

/** custom auth pages */
require_once CORE_PATH . DS . 'pages.php';

/** timber */
class BlankSite extends TimberSite {

	function __construct() {
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );

		if ( ! is_admin() ) {
			parent::__construct();
		}
	}

	function add_to_context( $context ) {
		$context['site'] = $this;
		$context['assets'] = ASSETS_VERSION;
		$context['site_language'] = BLOGINFO_LANGUAGE;
		$context['svg_sprite'] = SVG_SPRITE_URL;
        $context['general_fields'] = cache_general_fields();
        $context['localization'] = custom_localization();
        $context['notify'] = render_notify();
        $context['TEXTDOMAIN'] = TEXTDOMAIN;

        /** redirect rules */
        if(is_array($context['general_fields']['redirects']) && !empty($context['general_fields']['redirects'])){
            foreach($context['general_fields']['redirects'] as $rule){

                $old = $rule['old_url'];
                $new = $rule['new_url'];

                if (substr($_SERVER['REQUEST_URI'], -1) !== '/') {
                    $REQUEST_URI = $_SERVER['REQUEST_URI'] . '/';
                } else {
                    $REQUEST_URI = $_SERVER['REQUEST_URI'];
                }

                $old = str_replace(BLOGINFO_JUST_DOMAIN, "", str_replace("http://", "", str_replace("https://", "", $old)));

                if($REQUEST_URI == $old || $REQUEST_URI == $old."/"){
                    wp_redirect( $new, "301" );
                    exit;
                }
            }
        }

		return $context;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
        $twig->addFilter( new Twig_SimpleFilter( 'pr', 'pr' ) );
        $twig->addFilter( new Twig_SimpleFilter( 'picture', 'render_picture_tag' ) );
        $twig->addFilter( new Twig_SimpleFilter( 'nice_phone', 'nice_phone_format' ) );
        $twig->addFilter( new Twig_SimpleFilter( 'short_phone', 'short_phone_format' ) );
        $twig->addFilter( new Twig_SimpleFilter( 'fix_phone', 'fix_phone_format' ) );
        $twig->addFilter( new Twig_SimpleFilter( 'greetings', 'greetings' ) );
        $twig->addFilter( new Twig_SimpleFilter( 'to_options', 'to_options' ) );
        $twig->addFilter( new Twig_SimpleFilter( 'recent_posts', 'timber_recent_posts' ) );
        $twig->addFilter( new Twig_SimpleFilter( 'link', 'get_page_link_by_page_option_name' ) );
		return $twig;
	}

}

new BlankSite();

/** maintenance mode */
if(get_option('enable_maintenance_mode')){
	global $pagenow;
	if(!is_admin() && !is_user_logged_in() && $pagenow != "wp-login.php"){
		wp_die('<h1>'.get_option( 'maintenance_mode_title' ).'</h1><p>'.get_option( 'maintenance_mode_text' ).'</p>');
	}
}

/** timber html cache */
if(get_option('enable_html_cache')){
    define( 'TIMBER_CACHE_TIME', 48 * HOUR_IN_SECONDS );
} else {
    define( 'TIMBER_CACHE_TIME', false );
}
