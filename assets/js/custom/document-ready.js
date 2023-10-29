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

        /** shop item rating scroll */
        $('.single-catalog .ratingWrapper a').click(function(){
            if($('.feedbackWrapper').length){
                const yOffset = -70;
                const y = $('.feedbackWrapper')[0].getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({top: y, behavior: 'smooth'});
                return false;
            }
        });

        /** shop item tabs */
        $('.single-catalog .tabsWrapper li a').click(function(){
            let thisEl = $(this),
                parentEl = thisEl.parents('.tabsWrapper');
            parentEl.find('.active').removeClass('active');
            thisEl.parent().addClass('active');
            $('.descriptionWrapper').toggleClass('show');
            $('.characteristicsWrapper').toggleClass('show');
            return false;
        });

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
        if($('.sign-up input[type="password"], .password-reset input[type="password"]').length){
            $('.sign-up input[type="password"], .password-reset input[type="password"]').on('change keyup', function(){
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
        if($(".checkout input.user_phone").length){
            $(".checkout input.user_phone").mask('+380 00 000-00-00', {placeholder: "+380 __ ___-__-__"});
        }

        /** profile email manual confirm */
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
                        $('.middlePopup .resend').click(function(){
                            $('.middlePopup .resend').addClass('busy');
                            $('.middlePopup .resend .loader').addClass('show');
                            $.ajax({
                                type: "POST",
                                url: ajaxUrl,
                                dataType: "json",
                                cache: false,
                                data: {
                                    "action": "profile_resend_verification_code_to_email"
                                },
                                success : function (out) {
                                    if(out.status == 'ok'){
                                        $('.middlePopup .resend .label').attr('data-static-label', $('.middlePopup .resend .label').text());
                                        $('.middlePopup .resend .label').text(out.label);
                                        $('.middlePopup .resend .loader').removeClass('show');
                                        $('.middlePopup .codeInput input:first-child').focus();
                                        setTimeout(function() {
                                            $('.middlePopup .resend').removeClass('busy');
                                            $('.middlePopup .resend .label').text($('.middlePopup .resend .label').attr('data-static-label'));
                                            $('.middlePopup .resend .label').removeAttr('data-static-label');
                                        }, 60000);
                                    }
                                }
                            });
                            return false;
                        });
                    }
                }
            });
            return false;
        });

        /** profile sms manual confirm */
        $('.profile a[href="#confirm-phone"]').click(function(){
            prepare_middle_popup();
            $.ajax({
                type: "POST",
                url: ajaxUrl,
                dataType: "json",
                cache: false,
                data: {
                    "action": "profile_confirm_phone_number_content"
                },
                success : function (out) {
                    if(out.status == "ok"){
                        add_content_into_middle_popup(out.title, out.content);
                        let profileConfirmPhoneNumberForm = $('form.confirm-phone-number');
                        profileConfirmPhoneNumberForm.find('.codeInput input:first-child').focus();
                        profileConfirmPhoneNumberForm.find('.codeInput input').each(function(){
                            $(this).mask('0', {placeholder: "_"});
                            $(this).unbind('keyup');
                        });
                        profileConfirmPhoneNumberForm.find('.codeInput input').on('focus', function(){
                            if(!$(this).hasClass('input_1') && $(this).prev().val().trim() == ""){
                                $(this).prev().focus();
                            }
                        });
                        profileConfirmPhoneNumberForm.find('.codeInput input').on('keyup', function(){
                            $(this).removeClass('red');
                            if($(this).val().trim() != ""){
                                $(this).next().focus();
                            } else {
                                $(this).prev().focus();
                            }
                            if($(this).hasClass('input_4') && $(this).val().trim() != ""){

                                profileConfirmPhoneNumberForm.find('.codeInput input').addClass('disabled');
                                profileConfirmPhoneNumberForm.find('.codeInput input').blur();

                                $.ajax({
                                    type: "POST",
                                    url: ajaxUrl,
                                    dataType: 'json',
                                    data: new FormData(profileConfirmPhoneNumberForm[0]),
                                    contentType: false,
                                    cache: false,
                                    processData: false,
                                    success : function (out) {
                                        if(out.status == 'ok'){
                                            add_content_into_middle_popup(out.title, out.content);
                                            $('.profile form input[name="user_phone"]').next().html(out.confirmed);
                                        } else {
                                            $('.middlePopup .codeInput input').addClass('red');
                                            $('.middlePopup .codeInput input').removeClass('disabled');
                                            $('.middlePopup .codeInput input:last-child').focus();
                                        }
                                    }
                                });
                            }
                        });
                        $('.middlePopup .resend').click(function(){
                            $('.middlePopup .resend').addClass('busy');
                            $('.middlePopup .resend .loader').addClass('show');
                            $.ajax({
                                type: "POST",
                                url: ajaxUrl,
                                dataType: "json",
                                cache: false,
                                data: {
                                    "action": "profile_resend_verification_code_to_sms"
                                },
                                success : function (out) {
                                    if(out.status == 'ok'){
                                        $('.middlePopup .resend .label').attr('data-static-label', $('.middlePopup .resend .label').text());
                                        $('.middlePopup .resend .label').text(out.label);
                                        $('.middlePopup .resend .loader').removeClass('show');
                                        $('.middlePopup .codeInput input:first-child').focus();
                                        setTimeout(function() {
                                            $('.middlePopup .resend').removeClass('busy');
                                            $('.middlePopup .resend .label').text($('.middlePopup .resend .label').attr('data-static-label'));
                                            $('.middlePopup .resend .label').removeAttr('data-static-label');
                                        }, 60000);
                                    }
                                }
                            });
                            return false;
                        });
                    }
                }
            });
            return false;
        });

        /** profile password strength */
        if($('.profile input[type="password"][name="u_n_password"]').length){
            $('.profile input[type="password"][name="u_n_password"]').on('change keyup', function(){
                let thisEl = $(this),
                    recEl = thisEl.parent().next(),
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

        /** smart anchors */
        $('a').click(function(){

            let thisEL = $(this),
                thisAnchor = thisEL.attr('href').replace('#','');

            if($('.customBlockWrapper[data-anchor="'+thisAnchor+'"]').length){
                const yOffset = -70;
                const y = $('.customBlockWrapper[data-anchor="'+thisAnchor+'"]')[0].getBoundingClientRect().top + window.pageYOffset + yOffset;
                window.scrollTo({top: y, behavior: 'smooth'});
                return false;
            }
        });

        /** cart icon element */
        cartIconEl = $("header .cart");

        /** add to cart */
        $(".add_to_cart").click(function(){
            add_to_cart($(this));
            return false;
        });
        if(cartIconEl.length){
            count_cart();
            cartIconEl.click(function(){
                show_cart();
                return false;
            });
        }

        /** checkout cart */
        if($('.positionsWrapper .positions.busy').length){
            $('.positionsWrapper .positions').removeClass('busy');
            operate_positions();
        }

        /** checkout delivery type */
        $(".checkout .deliveryTypeWrapper input[name='delivery_type']").on('click', function() {
            let thisValue = $(this).val();
            $('.npTitle').removeClass('show');
            $('.npWrapper').removeClass('show');
            $('.upTitle').removeClass('show');
            $('.upWrapper').removeClass('show');
            $('.puTitle').removeClass('show');
            $('.puWrapper').removeClass('show');
            if(thisValue == 'np'){
                $('.npWrapper').addClass('show');
                $('.npTitle').addClass('show');
            }
            if(thisValue == 'up'){
                $('.upTitle').addClass('show');
                $('.upWrapper').addClass('show');
            }
            if(thisValue == 'pu'){
                $('.puTitle').addClass('show');
                $('.puWrapper').addClass('show');
            }
        });

        /** self pickup */
        const selfPickupPoints = $('#selfPickupPoints');
        if(selfPickupPoints){
            selfPickupPoints.select2({
                width: '100%',
                placeholder: 'Оберіть пункт самовивозу',
            });
            selfPickupPoints.on("change", function(e) {
                if($(this).val()){
                    $('.puWrapper .selfPickupPointInfo').removeClass('show');
                    $('.puWrapper .selfPickupPointInfo.'+$(this).val()).addClass('show');
                }
            });
        }

        /** self pickup maps */
        if($('.selfPickupPointInfo .map').length){
            $('.selfPickupPointInfo .map').each(function(){
                let mapEl = $(this),
                    mapElLat = parseFloat(mapEl.attr("data-lat")),
                    mapElLng = parseFloat(mapEl.attr("data-lng")),
                    mapElInfo = mapEl.attr("data-address");
                map_init(mapEl, {lat: mapElLat, lng: mapElLng}, mapElInfo);
            });
        }

        /** nova poshta */
        const citySearch = $('#citySearch');
        const postOfficeNumberSearch = $('#postOfficeNumberSearch');

        if(citySearch && postOfficeNumberSearch){

            citySearch.select2({
                width: '100%',
                placeholder: 'Вкажіть місто...',
                language: {
                    noResults: function() {
                        return "Міст не знайдено";
                    },
                    inputTooShort: function (args) {
                        let remainingChars = args.minimum - args.input.length;
                        return "Введіть перші " + remainingChars + " символа назви вашого міста або селища";
                    },
                    searching: function() {
                        return "Шукаємо...";
                    }
                },
                ajax: {
                    url: ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            action: 'get_cities_list',
                            search: params.term
                        };
                    },
                    processResults: function (data) {
                        let resultsArray = $.map(data, function (value, key) {
                            return {
                                id: key,
                                text: value
                            };
                        });
                        return {
                            results: resultsArray
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
            });

            citySearch.on("change", function(e) {
                $('input[name="user_np_city_name"]').val($(this).find("option:selected").text());
                if($(this).val()){
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl,
                        dataType: "json",
                        cache: false,
                        data: {
                            "action": "np_get_offices_by_city_ref",
                            "ref": $(this).val(),
                        },
                        success : function (out) {

                            if(out[0]?.text){
                                $('input[name="user_np_office_name"]').val(out[0].text);
                            }

                            postOfficeNumberSearch.val(null);
                            postOfficeNumberSearch.empty();
                            postOfficeNumberSearch.trigger('change');
                            postOfficeNumberSearch.select2({
                                placeholder: "Оберіть відділення",
                                language: {
                                    noResults: function() {
                                        return "Відділення не знайдено";
                                    }
                                },
                            });
                            postOfficeNumberSearch.prop('disabled', false).trigger('change.select2');

                            if(out.length > 0){
                                postOfficeNumberSearch.select2({
                                    data: out
                                });
                                postOfficeNumberSearch.prop('disabled', false).trigger('change.select2');
                            } else {
                                postOfficeNumberSearch.select2({
                                    placeholder: "Відділення не знайдено"
                                });
                                postOfficeNumberSearch.prop('disabled', true).trigger('change.select2');
                            }
                        }
                    });
                }
            });

            postOfficeNumberSearch.select2({
                width: '100%',
                language: {
                    noResults: function() {
                        return "Відділення не знайдено";
                    }
                },
            });

            if (postOfficeNumberSearch.prop('disabled')) {
                postOfficeNumberSearch.select2({
                    placeholder: "Спочатку оберіть місто або селище"
                });
            } else {
                postOfficeNumberSearch.select2({
                    placeholder: "Оберіть відділення"
                });
            }

            postOfficeNumberSearch.on("change", function(e) {
                $('input[name="user_np_office_name"]').val($(this).find("option:selected").text());
            });

        }

    });
})(jQuery);
