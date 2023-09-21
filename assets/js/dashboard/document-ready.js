/**
 * document ready
 */
(function ($) {
    $(document).ready(function () {

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
