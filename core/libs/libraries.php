<?php

if(!defined('ABSPATH')){exit;}

/** acf */
if (function_exists('get_fields')) {
    if(get_option('hide_acf')){
        add_filter('acf/settings/show_admin', '__return_false');
    }
    add_filter('acf/settings/save_json', 'my_acf_json_save_point');
    function my_acf_json_save_point( $path ) {
        // update path
        $path = THEME_PATH . DS . 'core' . DS . 'acf-json';
        // return
        return $path;
    }
    add_filter('acf/settings/load_json', 'my_acf_json_load_point');
    function my_acf_json_load_point( $paths ) {
        // remove original path (optional)
        unset($paths[0]);
        // append path
        $paths[] = THEME_PATH . DS . 'core' . DS . 'acf-json';
        // return
        return $paths;
    }
    // ACF nav menu field
    require_once CORE_PATH . DS . 'libs' . DS . 'acf-nav-menu-field' . DS . 'nav-menu-v5.php';
    // Options page for ACF
    $sub_page = array(
        'title' => __("Options", TEXTDOMAIN),
        'slug' => 'website-options',
        'capability' => 'edit_posts',
        'position'   => 30,
    );
    acf_add_options_page($sub_page);
    // Main label for ACF options pages
    acf_set_options_page_menu(__("Options", TEXTDOMAIN));
    acf_set_options_page_title( __("Options", TEXTDOMAIN) );

    // ACF options subpages
    acf_add_options_sub_page(array(
        'page_title'  => __('Overall', TEXTDOMAIN),
        'menu_title'  => __('Overall', TEXTDOMAIN),
        'slug' => 'overall-options',
        'parent_slug' => 'website-options',
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Header', TEXTDOMAIN),
        'menu_title'  => __('Header', TEXTDOMAIN),
        'slug' => 'header-options',
        'parent_slug' => 'website-options',
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Footer', TEXTDOMAIN),
        'menu_title'  => __('Footer', TEXTDOMAIN),
        'slug' => 'footer-options',
        'parent_slug' => 'website-options',
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Mobile', TEXTDOMAIN),
        'menu_title'  => __('Mobile', TEXTDOMAIN),
        'slug' => 'mobile-options',
        'parent_slug' => 'website-options',
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Emails', TEXTDOMAIN),
        'menu_title'  => __('Emails', TEXTDOMAIN),
        'slug' => 'emails-options',
        'parent_slug' => 'website-options',
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Auth', TEXTDOMAIN),
        'menu_title'  => __('Auth', TEXTDOMAIN),
        'slug' => 'auth-options',
        'parent_slug' => 'website-options',
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Shop', TEXTDOMAIN),
        'menu_title'  => __('Shop', TEXTDOMAIN),
        'slug' => 'shop-options',
        'parent_slug' => 'website-options',
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Profile', TEXTDOMAIN),
        'menu_title'  => __('Profile', TEXTDOMAIN),
        'slug' => 'profile-options',
        'parent_slug' => 'website-options',
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Cookie Popup', TEXTDOMAIN),
        'menu_title'  => __('Cookie Popup', TEXTDOMAIN),
        'slug' => 'cookie-popup-options',
        'parent_slug' => 'website-options',
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Page 404', TEXTDOMAIN),
        'menu_title'  => __('Page 404', TEXTDOMAIN),
        'slug' => 'page-404-options',
        'parent_slug' => 'website-options',
    ));
    acf_add_options_sub_page(array(
        'page_title'  => __('Redirects', TEXTDOMAIN),
        'menu_title'  => __('Redirects', TEXTDOMAIN),
        'slug' => 'redirects-options',
        'parent_slug' => 'website-options',
    ));
}

/** GeoIP */
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Db' . DS . 'Reader' . DS . 'Decoder.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Db' . DS . 'Reader' . DS . 'InvalidDatabaseException.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Db' . DS . 'Reader' . DS . 'Metadata.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Db' . DS . 'Reader' . DS . 'Util.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Db' . DS . 'Reader.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'ProviderInterface.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Exception' . DS . 'GeoIp2Exception.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Exception' . DS . 'AddressNotFoundException.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Model' . DS . 'AbstractModel.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Model' . DS . 'Country.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Model' . DS . 'City.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'AbstractRecord.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'AbstractPlaceRecord.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'MaxMind.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'Continent.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'Country.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'RepresentedCountry.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'Traits.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'City.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'Location.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'Postal.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Record' . DS . 'Subdivision.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Database' . DS . 'Reader.php';
require_once CORE_PATH . DS . 'libs' . DS . 'geoip' . DS . 'Util.php';

/** nova poshta */
require_once CORE_PATH . DS . 'libs' . DS . 'nova-poshta' . DS . 'NovaPoshtaApi2.php';

/** monopay */
require_once CORE_PATH . DS . 'libs' . DS . 'monopay' . DS . 'RequestBuilder.php';
require_once CORE_PATH . DS . 'libs' . DS . 'monopay' . DS . 'Webhook.php';
require_once CORE_PATH . DS . 'libs' . DS . 'monopay' . DS . 'Client.php';
require_once CORE_PATH . DS . 'libs' . DS . 'monopay' . DS . 'Payment.php';

/** timber */
require_once CORE_PATH . DS . 'libs' . DS . 'timber' . DS . 'timber.php';
Timber::$dirname = TIMBER_VIEWS;
