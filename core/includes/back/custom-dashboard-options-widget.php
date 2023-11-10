<?php

if(!defined('ABSPATH')){exit;}

if( current_user_can('edit_dashboard') ) {

    add_action('wp_dashboard_setup', 'custom_dashboard_widgets_manage' );
    function custom_dashboard_widgets_manage(){
        wp_add_dashboard_widget('dashboard_widget', __("System tweaks", TEXTDOMAIN), 'tweaks_widget_html');
    }

    function tweaks_widget_html(){

        $tweaks = array (

            /** Maintenance mode */
            array (
                'type'          => 'tab_start',
                'label'         => 'maintenance_mode',
                'description'   => __("Maintenance mode", TEXTDOMAIN)
            ),
            array (
                'type'          => 'checkbox',
                'label'         => 'enable_maintenance_mode',
                'description'   => __("Enable maintenance mode for anonymous users", TEXTDOMAIN)
            ),
            array (
                'type'          => 'title',
                'label'         => 'maintenance_mode_title',
                'description'   => __("Maintenance mode title", TEXTDOMAIN)
            ),
            array (
                'type'          => 'textarea',
                'label'          => 'maintenance_mode_text',
                'description'   => __("Maintenance mode text", TEXTDOMAIN)
            ),
            array (
                'type'          => 'tab_end',
            ),

            /** Resize at upload */
            array (
                'type'          => 'tab_start',
                'label'         => 'resize_at_upload',
                'description'   => __("Resize at upload", TEXTDOMAIN)
            ),
            array (
                'type'          => 'checkbox',
                'label'         => 'enable_resize_at_upload',
                'description'   => __("Enable resizing media while upload", TEXTDOMAIN)
            ),
            array (
                'type'          => 'select-multiple',
                'options'       => array (
                    'image/gif' => 'GIF',
                    'image/png' => 'PNG',
                    'image/jpeg' => 'JPEG',
                    'image/jpg' => 'JPG',
                ),
                'label'         => 'resize_at_upload_formats',
                'description'   => __("Resize at upload formats", TEXTDOMAIN)
            ),
            array (
                'type'          => 'number',
                'label'          => 'resize_upload_width',
                'description'   => __("Resize upload width", TEXTDOMAIN),
                'admin'         => true
            ),
            array (
                'type'          => 'number',
                'label'          => 'resize_upload_height',
                'description'   => __("Resize upload height", TEXTDOMAIN),
                'admin'         => true
            ),
            array (
                'type'          => 'number',
                'label'          => 'resize_upload_quality',
                'description'   => __("Resize upload quality", TEXTDOMAIN),
                'admin'         => true
            ),
            array (
                'type'          => 'tab_end',
            ),

            /** SMTP */
            array (
                'type'          => 'tab_start',
                'label'         => 'smpt_options',
                'description'   => __("SMTP options", TEXTDOMAIN)
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'enable_custom_smtp_server',
                'description'   => __("Enable custom SMTP server", TEXTDOMAIN),
            ),
            array (
                'type'          => 'title',
                'label'          => 'smtp_host',
                'description'   => __("SMTP host", TEXTDOMAIN)
            ),
            array (
                'type'          => 'number',
                'label'          => 'smtp_port',
                'description'   => __("SMTP port", TEXTDOMAIN)
            ),
            array (
                'type'          => 'title',
                'label'          => 'smtp_username',
                'description'   => __("SMTP username", TEXTDOMAIN)
            ),
            array (
                'type'          => 'title',
                'label'          => 'smtp_from',
                'description'   => __("SMTP from email", TEXTDOMAIN)
            ),
            array (
                'type'          => 'password',
                'label'          => 'smtp_password',
                'description'   => __("SMTP password", TEXTDOMAIN)
            ),
            array (
                'type'          => 'title',
                'label'          => 'smtp_from_name',
                'description'   => __("SMTP from name", TEXTDOMAIN)
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'smtp_secure',
                'description'   => __("Secure SMTP connection", TEXTDOMAIN),
            ),
            array (
                'type'          => 'tab_end',
            ),

            /** SMS */
            array (
                'type'          => 'tab_start',
                'label'         => 'sms_options',
                'description'   => __("SMS options", TEXTDOMAIN)
            ),
            array (
                'type'          => 'title',
                'label'          => 'sms_username',
                'description'   => __("SMS username", TEXTDOMAIN)
            ),
            array (
                'type'          => 'password',
                'label'          => 'sms_password',
                'description'   => __("SMS password", TEXTDOMAIN)
            ),
            array (
                'type'          => 'title',
                'label'          => 'sms_alpha_name',
                'description'   => __("SMS alpha name", TEXTDOMAIN)
            ),
            array (
                'type'          => 'tab_end',
            ),

            /** Custom code */
            array (
                'type'          => 'tab_start',
                'label'         => 'custom_code',
                'description'   => __("Custom code", TEXTDOMAIN)
            ),
            array (
                'type'          => 'code',
                'label'          => 'header_custom_code',
                'description'   => __("Header custom code", TEXTDOMAIN)
            ),
            array (
                'type'          => 'code',
                'label'          => 'footer_custom_code',
                'description'   => __("Footer custom code", TEXTDOMAIN)
            ),
            array (
                'type'          => 'tab_end',
            ),

            /** Other options */
            array (
                'type'          => 'tab_start',
                'label'         => 'other_options',
                'description'   => __("Other options", TEXTDOMAIN)
            ),
            array (
                'type'          => 'password',
                'label'          => 'google_maps_api_key',
                'description'   => __("Google maps API key", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'disable_all_updates',
                'description'   => __("Disable all updates", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'disable_customizer',
                'description'   => __("Disable customizer", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'disable_src_set',
                'description'   => __("Disable src set", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'remove_default_image_sizes',
                'description'   => __("Remove default image sizes", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'disable_core_privacy_tools',
                'description'   => __("Disable core privacy tools", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'enable_cyr3lat',
                'description'   => __("Enable CYR3LAT", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'disable_dns_prefetch',
                'description'   => __("Disable DNS prefetch", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'disable_rest_api',
                'description'   => __("Disable Rest API for anonymous users", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'disable_emojis',
                'description'   => __("Disable Emojis", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'disable_embeds',
                'description'   => __("Disable Embeds", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'hide_dashboard_widgets',
                'description'   => __("Disable dashboard widgets", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'hide_admin_top_bar',
                'description'   => __("Hide admin top bar", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'disable_admin_email_verification',
                'description'   => __("Disable admin email verification", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'disable_comments',
                'description'   => __("Disable comments", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'delete_child_media',
                'description'   => __("Delete child media", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'enable_html_cache',
                'description'   => __("Enable HTML cache", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'          => 'enable_minify',
                'description'   => __("Enable minify", TEXTDOMAIN),
            ),
            array (
                'type'          => 'checkbox',
                'label'         => 'inline_scripts_and_styles',
                'description'   => __("Inline scripts and styles", TEXTDOMAIN)
            ),
            array (
                'type'          => 'checkbox',
                'label'         => 'hide_acf',
                'description'   => __("Hide ACF", TEXTDOMAIN)
            ),
            array (
                'type'          => 'tab_end',
            )

        );
        echo Timber::compile( 'dashboard/tweaks/widget.twig', array(
            'localization' => custom_localization(),
            'tweaks' => $tweaks,
        ));
    }

}
