<?php

if(!defined('ABSPATH')){exit;}

/** assets theme version */

if(is_admin()){
	function dashboard_header_add_theme_update_button() {
		global $wp_admin_bar;
		$wp_admin_bar->add_menu( array(
			'id'    => 'update_assets_version',
			'title' => ASSETS_VERSION,
			'href'  => '#update_assets_version',
			'meta'  => array(
				'class' => 'update_assets_version',
				'title' => __("Update theme version", TEXTDOMAIN),
			),
		) );
	}
	add_action( 'wp_before_admin_bar_render', 'dashboard_header_add_theme_update_button' );
	function update_assets_version_header() {
		?>
		<style>
			.update_assets_version a:before{
				content: "\f185";
			}
			.update_assets_version a.working:before{
				content: "\f463";
			}
		</style>
		<script>
            (function ($) {
                $(document).ready(function () {
                    $(".update_assets_version a").click(function(){
                        var thisbutton = $(this);
                        $.ajax({
                            type: "POST",
                            url: ajaxurl,
                            dataType: "json",
                            cache: false,
                            data: {
                                action: "update_assets_version"
                            },
                            beforeSend: function() {
                                thisbutton.addClass("working");
                            },
                            success : function (out) {
                                thisbutton.text(out.new_assets_version);
                                thisbutton.blur();
                                thisbutton.removeClass("working");
                            }
                        });
                        return false;
                    });
                });
            })(jQuery);
		</script>
	<?php }
	add_action('admin_head', 'update_assets_version_header');

	function update_assets_version_function() {
		$new_assets_version = ASSETS_VERSION + 0.01;
		$new_assets_version = round($new_assets_version, 2);
		update_option('assets_version', $new_assets_version);
		$toJson['new_assets_version'] = $new_assets_version;
		echo json_encode($toJson);
        if (function_exists('wp_cache_flush')) { wp_cache_flush(); }
        if (function_exists('opcache_reset')) { opcache_reset(); }
        clear_svg_cache();
		exit;
	}
	add_action( 'wp_ajax_update_assets_version', 'update_assets_version_function' );

}
