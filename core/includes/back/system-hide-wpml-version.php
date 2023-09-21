<?php

if(!defined('ABSPATH')){exit;}

/** hide wpml version */
if(defined('ICL_LANGUAGE_CODE' )){
    global $sitepress;
    remove_action( 'wp_head', array( $sitepress, 'meta_generator_tag' ) );
}
