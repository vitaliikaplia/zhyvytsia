//@prepros-prepend plugins/jquery.ba-dotimeout.js
//@prepros-prepend plugins/jquery.cookie.js
//@prepros-prepend plugins/select2.min.js

/**
 * variables
 */
const ajaxUrl = "/wp-admin/admin-ajax.php";
const siteCookieDomain = "."+document.location.hostname.replace("www.","");

/**
 * Store options by ajax
 *
 * @param this_option_label
 * @param this_option_value
 * @param type
 */
function store_all_widget_options_by_ajax(this_option_label, this_option_value, type){

    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        dataType: "json",
        cache: false,
        data: {
            action: "change_widget_option",
            type: type,
            label: this_option_label,
            val: this_option_value
        },
        beforeSend: function() {
            // console.log(this_option_label);
            // console.log(this_option_value);
        },
        success : function (out) {
            // console.log(out);
            jQuery(".widget-options-inner").removeClass('wait');
        }
    });

}

/**
 * document ready
 */
(function ($) {
    $(document).ready(function () {

        /**
         * store option by checkbox
         */
        $('.widget-options-inner .widget-options-checkbox').change(function () {
            $(".widget-options-inner").addClass('wait');
            store_all_widget_options_by_ajax($(this).attr("id"), $(this).is(":checked"), 'checkbox');
        });

        /**
         * store option by number + text
         */
        $(".widget-options-inner .widget-options-number,.widget-options-inner .widget-options-text").bind('keyup', function(){
            $(".widget-options-inner").addClass('wait');
            $(this).doTimeout( 'text-type', 1000, function(){
                store_all_widget_options_by_ajax($(this).attr("id"), $(this).val().trim(), 'number');
            });
        });

        /**
         * store option by textarea + code
         */
        $(".widget-options-inner .widget-options-textarea,.widget-options-inner .widget-options-code").bind('keyup', function(){
            $(".widget-options-inner").addClass('wait');
            $(this).doTimeout( 'text-type', 1000, function(){
                store_all_widget_options_by_ajax($(this).attr("id"), $(this).val().trim(), 'textarea');
            });
        });

        /**
         * store option by checkbox
         */
        $('.widget-options-inner .widget-options-select-multiple').change(function () {
            $(".widget-options-inner").addClass('wait');
            store_all_widget_options_by_ajax($(this).attr("id"), $(this).val(), 'select-multiple');
        });

        /** tweaks tabs */
        $('.tweakTabs li a').click(function(){
            $(this).parent().parent().find('.active').removeClass('active');
            $(this).parent().addClass('active');
            $('.tweakGroups .group.active').removeClass('active');
            var currElIndex = parseInt($(this).parent().index()) + 1;
            $('.tweakGroups .group:nth-child('+currElIndex+')').addClass('active');
            $.cookie('current_tweak_options_tab', currElIndex);
            return false;
        });
        if($.cookie('current_tweak_options_tab')){
            $('.tweakTabs li:nth-child('+$.cookie('current_tweak_options_tab')+') a').trigger('click');
        }

        /** tweaks multi select */
        $('.widget-options-select-multiple').select2({
            closeOnSelect: false
        });

    });
})(jQuery);
