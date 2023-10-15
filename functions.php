<?php

if(!defined('ABSPATH')){exit;}

/** let's go! */
require_once 'core' . DIRECTORY_SEPARATOR . 'init.php';

//$photos_raw = file_get_contents(ABSPATH . 'wp-content' . DS . 'photos.txt');

//$dom = new DOMDocument();
//$dom->loadHTML($photos_raw);
//$images = $dom->getElementsByTagName('img');
//$image_srcs = array();
//foreach ($images as $image) {
//    if($image->getAttribute('src') != "/local/templates/main-v2022/img/1.png"){
//        $src = $image->getAttribute('src');
//    } else {
//        $src = $image->getAttribute('data-src');
//    }
//    if (strpos($src, '/geopro-photos/pics/original/060/617/') !== false) {
//        $image_srcs[] = $src;
//        $filename = basename($src);
//        $image_data = file_get_contents($src);
//        file_put_contents(ABSPATH . 'wp-content' . DS . 'ppp' . DS . $filename, $image_data);
//    }
//}

//pr($image_srcs);
//global $_wp_theme_features;
//pr($_wp_theme_features);
// Loop through each support item and display it
//foreach ( $theme_supports as $feature => $value ) {
//    echo '<p>' . $feature . '</p>';
//}


//pr($GLOBALS['wp_filter']);

//function disable_gutenberg_word_block_count() {
//    add_filter( 'block_editor_settings', 'remove_word_block_count', 10, 2 );
//}
//
//function remove_word_block_count( $settings, $post ) {
//    if ( isset( $settings['wordCount']['showWordCount'] ) ) {
//        $settings['wordCount']['showWordCount'] = false;
//    }
//    if ( isset( $settings['__experimentalBlockDirectory']['count'] ) ) {
//        $settings['__experimentalBlockDirectory']['count'] = false;
//    }
//    $settings['richEditingEnabled'] = true;
////    pr($settings);
//    return $settings;
//}
//
//add_action( 'after_setup_theme', 'disable_gutenberg_word_block_count' );

//echo Timber::compile( 'mail/sign-up.twig', array(
//    'TEXTDOMAIN' => TEXTDOMAIN,
//    'BLOGINFO_NAME' => BLOGINFO_NAME,
//    'title' => __("Welcome to Zhyvytsia", TEXTDOMAIN),
//    'preheader' => __("Your verification code", TEXTDOMAIN),
//    'user_email_verification_code' => emoji_numbers(1234567890),
//    'verification_link' => get_page_link_by_page_option_name('profile_page') . "?data="
//));
//exit;

//pr(get_session_info('::1'));
//pr(get_option('admin_email'));
//$auth = get_field('auth', 'options');
//pr($auth['mail_templates']['sign_up_subject']);
//pr(fix_phone_format('0672089900'));
