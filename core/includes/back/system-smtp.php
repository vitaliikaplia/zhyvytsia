<?php

if(!defined('ABSPATH')){exit;}

add_action( 'phpmailer_init', 'smtp_fix_phpmailer_init' );
function smtp_fix_phpmailer_init( $phpmailer ) {

    if(
        get_option('enable_custom_smtp_server') &&
        ( get_option('smtp_host') ) &&
        ( get_option('smtp_port') ) &&
        ( get_option('smtp_username') ) &&
        ( get_option('smtp_password') ) &&
        ( get_option('smtp_from_name') )
    ){
        $phpmailer->Host = get_option('smtp_host');
        $phpmailer->Port = get_option('smtp_port');
        $phpmailer->Username = get_option('smtp_username');
        $phpmailer->Password = get_option('smtp_password');
        $phpmailer->SMTPAuth = true;
        $phpmailer->From     = get_option('smtp_username');
        $phpmailer->FromName = get_option('smtp_from_name');
        if(get_option('smtp_secure')){
            $phpmailer->SMTPSecure = 'ssl';
        } else {
            $phpmailer->SMTPSecure = false;
        }
        $phpmailer->IsSMTP();
    }

}
