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
                if($(e.target).hasClass('cookiePopupWrapper')){
                    $.cookie("user-cookies-accepted", true);
                    $('.cookiePopupWrapper').removeClass('show');
                    return false;
                }
            });
            $('.closeCookiePopup a').click(function(e){
                $.cookie("user-cookies-accepted", true);
                $('.cookiePopupWrapper').removeClass('show');
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
        if($('.positionsWidget .positions.busy').length){
            $('.positionsWidget .positions').removeClass('busy');
            operate_positions();
        }

        /** checkout delivery type */
        $(".checkout .deliveryTypeWidget input[name='delivery_type']").on('click', function() {
            let thisValue = $(this).val();
            $('.npWidget').removeClass('show');
            $('.upWidget').removeClass('show');
            $('.puWidget').removeClass('show');
            $('.cod_payment_type').addClass('hidden');
            $('.payment_upon_receipt_type').addClass('hidden');
            $('input[name="payment_type"]').prop('checked', false);
            if(thisValue == 'up'){
                $('.upWidget').addClass('show');
            }
            if(thisValue == 'np'){
                $('.npWidget').addClass('show');
                $('.cod_payment_type').removeClass('hidden');
            }
            if(thisValue == 'pu'){
                $('.puWidget').addClass('show');
                $('.payment_upon_receipt_type').removeClass('hidden');
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
                    $('.puWidget .selfPickupPointInfo').removeClass('show');
                    $('.puWidget .selfPickupPointInfo.'+$(this).val()).addClass('show');
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

                if($(this).find("option:selected").text()){
                    $('input[name="user_np_city_name"]').val($(this).find("option:selected").text());
                }

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
                                    placeholder: "Оберіть відділення",
                                    language: {
                                        noResults: function() {
                                            return "Відділення не знайдено";
                                        }
                                    },
                                    data: out
                                });
                                postOfficeNumberSearch.val(null).trigger('change');
                                postOfficeNumberSearch.prop('disabled', false).trigger('change.select2');

                                // if(out[0]?.text){
                                //     $('input[name="user_np_office_name"]').val(out[0]?.text);
                                // }

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

        /** submit checkout form */
        $('.checkout form').submit(function(){
            const thisForm = $(this);
            const yOffset = ($('header').outerHeight()+20)*-1;
            let letThisHappen = true,
                isPersonalInformationComplete = true,
                deliveryType,
                isDeliveryTypeSelected = true,
                isDeliveryInformationComplete = true,
                isPaymentTypeSelected = true,
                paymentType,
                errorMessage = "",
                isScrolled = false;

            thisForm.find('.informationWidget input[type="text"],.informationWidget input[type="email"]').each(function(){
                if($(this).val().trim() == ""){
                    isPersonalInformationComplete = false;
                    if(!isScrolled){
                        // thisForm.find('.informationWidget input').first().focus();
                        const y = $('.informationWidget')[0].getBoundingClientRect().top + window.pageYOffset + yOffset;
                        window.scrollTo({top: y, behavior: 'smooth'});
                        isScrolled = true;
                    }
                }
            });
            if(!isPersonalInformationComplete){
                letThisHappen = false;
                errorMessage += '<p>Вкажіть вашу персональну інформацію</p>';
            }

            if(!thisForm.find('input[type="radio"][name="delivery_type"]:checked').length){
                letThisHappen = false;
                isDeliveryTypeSelected = false;
                errorMessage += '<p>Вкажіть метод доставки</p>';
                if(!isScrolled){
                    const y = $('.deliveryTypeWidget')[0].getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({top: y, behavior: 'smooth'});
                    isScrolled = true;
                }
            } else {
                deliveryType = thisForm.find('input[type="radio"][name="delivery_type"]:checked').val();
            }

            if(!thisForm.find('input[type="radio"][name="payment_type"]:checked').length){
                letThisHappen = false;
                isPaymentTypeSelected = false;
                errorMessage += '<p>Вкажіть метод оплати</p>';
                if(!isScrolled){
                    const y = $('.paymentTypeWidget')[0].getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({top: y, behavior: 'smooth'});
                    isScrolled = true;
                }
            } else {
                paymentType = thisForm.find('input[type="radio"][name="payment_type"]:checked').val();
            }

            if(deliveryType == 'up'){
                thisForm.find('.upWidget input[type="text"]').each(function(){
                    if($(this).val().trim() == ""){
                        isDeliveryInformationComplete = false;
                        if(!isScrolled){
                            // thisForm.find('.upWidget input').first().focus();
                            const y = $('.upWidget')[0].getBoundingClientRect().top + window.pageYOffset + yOffset;
                            window.scrollTo({top: y, behavior: 'smooth'});
                            isScrolled = true;
                        }
                    }
                });
                if(!isDeliveryInformationComplete){
                    letThisHappen = false;
                    errorMessage += '<p>Вкажіть адресу доставки</p>';
                }
            } else if(deliveryType == 'np'){
                if (!thisForm.find('#citySearch').val() || !thisForm.find('#postOfficeNumberSearch').val()){
                    letThisHappen = false;
                    isDeliveryInformationComplete = false;
                    errorMessage += '<p>Вкажіть місто та відділення Нової Пошти</p>';
                    if(!isScrolled){
                        const y = $('.npWidget')[0].getBoundingClientRect().top + window.pageYOffset + yOffset;
                        window.scrollTo({top: y, behavior: 'smooth'});
                        isScrolled = true;
                    }
                }
            } else if(deliveryType == 'pu'){
                if (!thisForm.find('#selfPickupPoints').val()){
                    letThisHappen = false;
                    isDeliveryInformationComplete = false;
                    errorMessage += '<p>Вкажіть пункт самовивозу</p>';
                    if(!isScrolled){
                        const y = $('.puWidget')[0].getBoundingClientRect().top + window.pageYOffset + yOffset;
                        window.scrollTo({top: y, behavior: 'smooth'});
                        isScrolled = true;
                    }
                }
            }

            if(!letThisHappen){
                if(!$('.checkout .orderWidget .notify').length){
                    $('.checkout .orderWidget .total').after('<div class="notify"></div>');
                }
                $('.checkout .orderWidget .notify').addClass('error show');
                $('.checkout .orderWidget .notify').html(errorMessage);
                return false;
            }
        });

        /** post custom likes increment */
        if($('.blogPageWrapper .social .like').length){
            $('.blogPageWrapper .social .like').click(function(){
                let thisLikesEl = $(this);
                if(!thisLikesEl.hasClass('hold')){
                    thisLikesEl.addClass('hold');
                    $.ajax({
                        type: "POST",
                        url: ajaxUrl,
                        dataType: "json",
                        cache: false,
                        data: {
                            "action": "like_this_post",
                            "postId": parseInt(thisLikesEl.data('id')),
                            "currentPageUrl": document.URL
                        },
                        success : function (out) {
                            if(out.status == "ok"){
                                thisLikesEl.removeClass('hold');
                                if(out.operation == 'minus'){
                                    thisLikesEl.removeClass('liked');
                                    $.cookie('post-id-'+out.post_id+'-liked', null);
                                } else {
                                    $.cookie('post-id-'+out.post_id+'-liked', true);
                                    thisLikesEl.addClass('liked');
                                }
                                thisLikesEl.find('span').html(out.html);
                            }
                        }
                    });
                }
                return false;
            });
            $('.blogPageWrapper .social .like').each(function(){
                let thisLikesEl = $(this),
                    thisLikePostId = parseInt(thisLikesEl.data('id'));

                if($.cookie('post-id-'+thisLikePostId+'-liked')){
                    thisLikesEl.addClass('liked');
                }
            });
        }

        /** Share icons */
        if($(".share .icon").length){
            $(".share .icon").click(function(){
                let thisEl = $(this),
                    loc = window.location.href,
                    title = $('.postTitle').text();
                if(thisEl.hasClass("tw")){
                    window.open('http://twitter.com/share?url=' + loc + '&text=' + title + '&', 'twitterwindow', 'height=450, width=550, top='+($(window).height()/2 - 225) +', left='+$(window).width()/2 +', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
                } else if(thisEl.hasClass("fb")){
                    window.open('https://www.facebook.com/sharer/sharer.php?u=' + loc, 'facebookwindow', 'height=450, width=550, top='+($(window).height()/2 - 225) +', left='+$(window).width()/2 +', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
                } else if(thisEl.hasClass("ld")){
                    window.open('https://www.linkedin.com/sharing/share-offsite/?url=' + loc + '&summary=' + title, 'linkedinwindow', 'height=450, width=550, top='+($(window).height()/2 - 225) +', left='+$(window).width()/2 +', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
                } else if(thisEl.hasClass("pi")){
                    window.open('http://pinterest.com/pin/create/link/?url=' + loc + '&subject=' + title, 'okwindow', 'height=450, width=550, top='+($(window).height()/2 - 225) +', left='+$(window).width()/2 +', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
                }
                return false;
            });
        }

        /** custom form */
        if($('form.feedbackForm').length){
            $('form.feedbackForm').each(function(){
                feedback_form_submit($(this));
            });
        }

    });
})(jQuery);
