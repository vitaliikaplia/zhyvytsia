/**
 * window load scroll resize
 */
(function ($) {
    $(window).on("load scroll resize", function () {

        if(footerEl && !$('body').hasClass('profile') && !$('body').hasClass('checkout') && !$('body').hasClass('order') && window.scrollY != 0){
            if (isInViewport(footerEl)) {
                if (!bodyEl.classList.contains('footer-in-viewport')) {
                    bodyEl.classList.add('footer-in-viewport');
                }
            } else {
                if (bodyEl.classList.contains('footer-in-viewport')) {
                    bodyEl.classList.remove('footer-in-viewport');
                }
            }
        }

    });
})(jQuery);
