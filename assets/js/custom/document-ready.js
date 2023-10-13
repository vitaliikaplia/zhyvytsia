/** fixing chrome preload transition issue */
window.addEventListener("DOMContentLoaded", function (){
    document.body.classList.remove('preload');
});

/**
 * document ready
 */
(function ($) {
    $(document).ready(function () {

        /** header */
        $("body").headroom({
            tolerance: {
                up: 14,
                down: 16
            }
        });

        /** вітання */
        const allGreetings = document.querySelectorAll('.main-hero .suffix');
        allGreetings.forEach(function (greetings) {
            run_greetings(greetings);
        });

        /** Cookie Pop-Up */
        if(!$.cookie("user-cookies-accepted") && $('.cookiePopupWrapper').length){
            setTimeout(function(){
                $('.cookiePopupWrapper').addClass('show');
            }, 2000);
            $('.cookiePopupWrapper').click(function(e){
                if($(e.target).hasClass('cookiePopupWrapper') || $(e.target).is('span')){
                    $.cookie("user-cookies-accepted", true);
                    $('.cookiePopupWrapper').removeClass('show');
                }
                return false;
            });
        }

        /** expert areas */
        const feedbackAllSliders = document.querySelectorAll('.feedbackWrapper .feedback');
        feedbackAllSliders.forEach(function (slider) {
            run_feedback_slider(slider);
        });

        /** about */
        const aboutAllSliders = document.querySelectorAll('.aboutGalleryWrapper .aboutGallery');
        aboutAllSliders.forEach(function (slider) {
            run_about_slider(slider);
        });

        /** custom form */
        if($('.main-form .customBlock form').length){
            $('.main-form .customBlock form').each(function(){
                custom_form_submit($(this));
            });
        }

        /** shop item slider */
        let frameSingle,
            thumbnailsSingle;
        if($('.singleCatalogItem .thumbnails').length){
            thumbnailsSingle = new Swiper(".singleCatalogItem .thumbnails", {
                loop: true,
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true
            });
        }
        if($('.singleCatalogItem .frame').length){
            frameSingle = new Swiper(".singleCatalogItem .frame", {
                loop: true,
                spaceBetween: 10,
                autoHeight: true,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                thumbs: {
                    swiper: thumbnailsSingle,
                },
            });
        }

        /** shop item description */
        if($('.descriptionWrapper button').length){
            $('.descriptionWrapper button').click(function(){
                let thisEl = $(this),
                    prevEl = thisEl.prev();
                if(prevEl.hasClass('short')){
                    prevEl.removeClass('short');
                    thisEl.text('Показати менше');
                } else {
                    prevEl.addClass('short');
                    thisEl.text('Показати більше');
                }
            });
        }

        /** auth show / hide password */
        const showHideElements = document.querySelectorAll('form .showHide');
        if (showHideElements) {
            showHideElements.forEach(function(showHideElement) {
                const passwordField = showHideElement.nextElementSibling;
                if(passwordField) {
                    showHideElement.addEventListener('click', function() {
                        if (passwordField.type == 'password') {
                            passwordField.type = "text";
                            this.setAttribute('data-show', this.textContent);
                            this.textContent = this.getAttribute('data-hide');
                            this.classList.remove('show');
                            this.classList.add('hide');
                        } else {
                            passwordField.type = "password";
                            this.textContent = this.getAttribute('data-show');
                            this.classList.remove('hide');
                            this.classList.add('show');
                        }
                    });
                }
            });
        }

        /** auth password strength */
        if($('.sign-up input[type="password"]').length){
            $('.sign-up input[type="password"]').on('change keyup', function(){
                let thisEl = $(this),
                    recEl = thisEl.next(),
                    thisVal = thisEl.val()?.trim(),
                    passResult = check_password_strength(thisVal);
                if(!Object.values(passResult).some(value => value === false)){
                    recEl.removeClass('show');
                } else {
                    recEl.addClass('show');
                    recEl.find('.length').toggleClass('show', !passResult.p_length);
                    recEl.find('.lowercase').toggleClass('show', !passResult.lowercase);
                    recEl.find('.uppercase').toggleClass('show', !passResult.uppercase);
                    recEl.find('.digits').toggleClass('show', !passResult.digits);
                    recEl.find('.special').toggleClass('show', !passResult.special);
                }
            });
        }

        /** profile phone mask */
        if($(".profile input.user_phone").length){
            $(".profile input.user_phone").mask('+380 00 000-00-00', {placeholder: "+380 __ ___-__-__"});
        }

        $('.profile a[href="#confirm-email"]').click(function(){
            prepare_middle_popup();
            $.ajax({
                type: "POST",
                url: ajaxUrl,
                dataType: "json",
                cache: false,
                data: {
                    "action": "profile_confirm_email_content"
                },
                success : function (out) {
                    if(out.status == "ok") {
                        add_content_into_middle_popup(out.title, out.content);
                        let profileConfirmEmailForm = $('form.profile-confirm-email');
                        profileConfirmEmailForm.find('.codeInput input:first-child').focus();
                        profileConfirmEmailForm.find('.codeInput input').each(function(){
                            $(this).mask('0', {placeholder: "_"});
                            $(this).unbind('keyup');
                        });
                        profileConfirmEmailForm.find('.codeInput input').on('focus', function(){
                            if(!$(this).hasClass('input_1') && $(this).prev().val().trim() == ""){
                                $(this).prev().focus();
                            }
                        });
                        profileConfirmEmailForm.find('.codeInput input').on('keyup', function(){
                            $(this).removeClass('red');
                            if($(this).val().trim() != ""){
                                $(this).next().focus();
                            } else {
                                $(this).prev().focus();
                            }
                            if($(this).hasClass('input_4') && $(this).val().trim() != ""){
                                profileConfirmEmailForm.find('.codeInput input').addClass('disabled');
                                profileConfirmEmailForm.find('.codeInput input').blur();
                                $.ajax({
                                    type: "POST",
                                    url: ajaxUrl,
                                    dataType: 'json',
                                    data: new FormData(profileConfirmEmailForm[0]),
                                    contentType: false,
                                    cache: false,
                                    processData: false,
                                    success : function (out) {
                                        if(out.status == 'ok'){
                                            add_content_into_middle_popup(out.title, out.content);
                                            $('.profile form input[type="email"]').next().html(out.confirmed);
                                        } else {
                                            profileConfirmEmailForm.find('.codeInput input').addClass('red');
                                            profileConfirmEmailForm.find('.codeInput input').removeClass('disabled');
                                            profileConfirmEmailForm.find('.codeInput input:last-child').focus();
                                        }
                                    }
                                });
                            }
                        });

                    }
                }
            });
            return false;
        });

    });
})(jQuery);
