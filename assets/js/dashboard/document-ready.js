/**
 * document ready
 */
(function ($) {
    $(document).ready(function () {

        if($("input#user_phone").length){
            $("input#user_phone").mask('+380 00 000-00-00', {placeholder: "+380 __ ___-__-__"});
        }

        /** change item status */
        $('.column-catalog_status select').on('change', function() {

            const orderStatusSelect = $(this);
            let postIdForChange = orderStatusSelect.attr("data-post-id");
            let newOrderStatus = orderStatusSelect.val();

            orderStatusSelect.attr("disabled","disabled");

            $.ajax({
                type: "POST",
                url: "/wp-admin/admin-ajax.php",
                dataType: "json",
                cache: false,
                data: {
                    "action": "dashboard_change_order_status",
                    "new_status": newOrderStatus,
                    "post_id": postIdForChange
                },
                success : function (out) {
                    if(out.status == "ok"){
                        orderStatusSelect.removeAttr("disabled");
                    }
                }
            });
        });

    });
})(jQuery);

/**
 * JS inside blocks
 */
if( window.acf ) {

    window.acf.addAction( 'render_block_preview', function( elem, blockDetails ) {

        if(blockDetails.name == 'acf/main-feedback'){
            run_feedback_slider(elem.get(0).querySelector('.feedbackWrapper .feedback'));
        }

        if(blockDetails.name == 'acf/main-about'){
            run_about_slider(elem.get(0).querySelector('.aboutGalleryWrapper .aboutGallery'));
        }

        elem.find('a,button').click(function(){
            return false;
        });

    } );
}
