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

            const itemStatusSelect = $(this);
            let postIdForChange = itemStatusSelect.attr("data-post-id");
            let newItemStatus = itemStatusSelect.val();

            itemStatusSelect.attr("disabled","disabled");

            $.ajax({
                type: "POST",
                url: "/wp-admin/admin-ajax.php",
                dataType: "json",
                cache: false,
                data: {
                    "action": "dashboard_change_item_status",
                    "new_status": newItemStatus,
                    "post_id": postIdForChange
                },
                success : function (out) {
                    if(out.status == "ok"){
                        itemStatusSelect.removeAttr("disabled");
                    }
                }
            });
        });

        /** order status select2 */
        if($('.order_status_select').length){
            $('.order_status_select').select2({
                width: '100%',
                placeholder: 'Оберіть статус замовлення',
            });
        }

        /** change order status */
        $('.order_status_select').on('change', function() {

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

        /** order note autogrow and save logic */
        if($('.line.note.order textarea').length){
            let thisEl = $('.line.note.order textarea'),
                postIdForChange = thisEl.attr("data-post-id");
            thisEl.autogrow();
            thisEl.bind('keyup', function(){
                $(this).doTimeout( 'text-type', 500, function(){
                    thisEl.addClass('wait');
                    jQuery.ajax({
                        type: "POST",
                        url: "/wp-admin/admin-ajax.php",
                        dataType: "json",
                        cache: false,
                        data: {
                            "action": "dashboard_change_order_note",
                            "note": thisEl.val().trim(),
                            "post_id": postIdForChange
                        },
                        beforeSend: function() {},
                        success : function (out) {
                            thisEl.removeClass('wait');
                        }
                    });
                });
            });
        }

        /** order note popup */
        $('.edit-order-note').click(function(){
            let thisEl = $(this),
                thisElId = thisEl.attr("data-post-id");
            jQuery.ajax({
                type: "POST",
                url: "/wp-admin/admin-ajax.php",
                dataType: "json",
                cache: false,
                data: {
                    "action": "dashboard_get_order_note",
                    "post_id": thisElId
                },
                success : function (out) {
                    $('body').append('<div class="orderNotePopupEditorWrapper"><textarea>'+out.note+'</textarea></div>');
                    let popup = $('.orderNotePopupEditorWrapper'),
                        editor = popup.find('textarea');
                    editor.focus();
                    $('.orderNotePopupEditorWrapper').click(function(e){
                        if($(e.target).attr('class') == "orderNotePopupEditorWrapper"){
                            saveAndCloseOrderNotePopup(popup, editor, thisElId);
                        }
                    });
                    editor.keyup(function(e) {
                        if (e.key === "Escape") {
                            saveAndCloseOrderNotePopup(popup, editor, thisElId);
                        }
                    });
                }
            });
        });

        /** qr columns */
        if($('.get_qr').length){
            $('.get_qr').each(function(){
                const thisEl = $(this);
                const thisLink = thisEl.attr('data-link');
                const thisTitle = thisEl.attr('data-title');
                const qr = new QRCode({
                    msg   :  thisLink,
                    dim   :   256,
                    pad   :   0,
                    mtx   :  -1,
                    ecl   :  "L",
                    ecb   :   1,
                    pal   : ["#000", "#fff"],
                    vrb   :   0
                });
                thisEl.html(qr);
                const qrEl = thisEl.find('svg');
                qrEl.click(function(){
                    const svgString = new XMLSerializer().serializeToString(qrEl[0]);
                    const svgBlob = new Blob([svgString], {type: 'image/svg+xml'});
                    const svgUrl = URL.createObjectURL(svgBlob);
                    const tempLink = $('<a>', {
                        href: svgUrl,
                        download: thisTitle + '.svg'
                    });
                    tempLink.appendTo('body').get(0).click();
                    tempLink.remove();
                });
            });
        }

    });
})(jQuery);

/**
 * editing order note function
 */
function saveAndCloseOrderNotePopup(popup, editor, thisElId) {
    jQuery.ajax({
        type: "POST",
        url: "/wp-admin/admin-ajax.php",
        dataType: "json",
        cache: false,
        data: {
            "action": "dashboard_change_order_note",
            "note": editor.val().trim(),
            "post_id": thisElId
        },
        success: function(out) {
            popup.remove();
        }
    });
}

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
