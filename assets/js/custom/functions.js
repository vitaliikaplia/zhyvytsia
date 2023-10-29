/** if valid email */
function isValidEmailAddress(emailAddress) {
    let pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}

/** вітання */
function run_greetings(element){
    if(!element.classList.contains('initialized')){
        let typed = new Typed(element, {
            strings: element.parentNode.getAttribute('data-greetings').split(','),
            typeSpeed: 100,
            loop: true,
            startDelay: 200,
            backDelay: 1500
        });
        element.classList.add('initialized');
    }
}

/** відгуки */
function run_feedback_slider(element){
    let autoplay = {};
    if(element.dataset.interval){
        autoplay = {
            delay: parseInt(element.dataset.interval),
            disableOnInteraction: false
        };
    }
    let swiperSlider = new Swiper(element, {
        direction: 'horizontal',
        slidesPerView: 4,
        spaceBetween: 40,
        loop: true,
        autoplay,
        pagination: {
            el: ".feedback-swiper-pagination",
            clickable: true
        },
        grabCursor: true
    });
}

/** про нас */
function run_about_slider(element){
    let swiperSlider = new Swiper(element, {
        direction: 'horizontal',
        slidesPerView: 8,
        spaceBetween: 0,
        loop: true,
        grabCursor: true
    });

}

/** custom form submit */
function custom_form_submit(thisFormEl){

    if(thisFormEl.find('input[id="form_name"]').val() == ''){
        thisFormEl.find('input[id="form_name"]').val(document.title);
    }

    if(thisFormEl.find('input[id="form_url"]').val() == ''){
        thisFormEl.find('input[id="form_url"]').val(document.location.href);
    }

    /** select placeholder trick */
    thisFormEl.find('select').change(function() {
        if (jQuery(this).val() != 'null') {
            jQuery(this).addClass('selected');
        }
    });

    /** form textarea fields autogrow */
    if(thisFormEl.find('textarea').length){
        thisFormEl.find('textarea').each(function(){
            var thisEl = jQuery(this),
                thisTextAreaHeight = thisEl.outerHeight();
            thisEl.autogrow();
            thisEl.css("height",thisTextAreaHeight);
        });
    }

    thisFormEl.find("button").click(function(){
        thisFormEl.submit();
        return false;
    });

    thisFormEl.find(".required").bind('click keyup', function(){
        let thisEl = $(this);
        setTimeout(function () {
            thisEl.removeClass("red");
        }, 400);
    });

    thisFormEl.submit(function(e){

        e.preventDefault();

        var readyToSend;

        if(!thisFormEl.hasClass("busy")){

            readyToSend = true;

            if(thisFormEl.find("input.required").length){
                thisFormEl.find("input.required").each(function(){
                    if(jQuery(this).val().trim() == ""){
                        readyToSend = false;
                        jQuery(this).addClass("red");
                        thisFormEl.find(".red").first().focus();
                    } else {
                        jQuery(this).removeClass("red");
                    }
                });
            }

            if(thisFormEl.find("textarea.required").length){
                thisFormEl.find("textarea.required").each(function(){
                    if(jQuery(this).val().trim() == ""){
                        readyToSend = false;
                        jQuery(this).addClass("red");
                    } else {
                        jQuery(this).removeClass("red");
                    }
                });
            }

            if(thisFormEl.find("select.required").length){
                thisFormEl.find("select.required").each(function(){
                    if(jQuery(this).val() == null){
                        readyToSend = false;
                        jQuery(this).addClass("red");
                    } else {
                        jQuery(this).removeClass("red");
                    }
                });
            }

            if(thisFormEl.find("input.email").length){
                thisFormEl.find("input.email").each(function(){
                    if(!isValidEmailAddress(jQuery(this).val().trim())){
                        readyToSend = false;
                        jQuery(this).addClass("red");
                    } else {
                        jQuery(this).removeClass("red");
                    }
                });
            }

            if(readyToSend == true){

                thisFormEl.addClass("busy");

                thisFormEl.find('input, button').each(function(){
                    jQuery(this).blur();
                });

                const formData = new FormData(thisFormEl[0]);
                for (const [name, value] of new FormData(thisFormEl[0])) {
                    const encodedValue = encodeURIComponent(value);
                    formData.append(name, encodedValue);
                }

                jQuery.ajax({
                    type: "POST",
                    url: ajaxUrl,
                    dataType: 'json',
                    data: new FormData(thisFormEl[0]),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success : function (out) {
                        console.log(out);
                        if(out.status == "ok"){
                            thisFormEl.find('select').addClass('required').removeClass('selected');
                            thisFormEl.removeClass("busy");
                            thisFormEl.trigger("reset");
                        }
                    }
                });

            }

        }

    });

}

/** перевірка паролю */
function check_password_strength(password) {
    let result = new Array();

    // Regular expressions for different password requirements
    const regexLowercase = /[a-z]/;
    const regexUppercase = /[A-Z]/;
    const regexDigit = /[0-9]/;
    const regexSpecialChar = /[^a-zA-Z0-9]/;

    // Check password length
    if (password.length < 8) {
        result['p_length'] = false;
    } else {
        result['p_length'] = true;
    }

    // Check lowercase letters
    if (!regexLowercase.test(password)) {
        result['lowercase'] = false;
    } else {
        result['lowercase'] = true;
    }

    // Check uppercase letters
    if (!regexUppercase.test(password)) {
        result['uppercase'] = false;
    } else {
        result['uppercase'] = true;
    }

    // Check digits
    if (!regexDigit.test(password)) {
        result['digits'] = false;
    } else {
        result['digits'] = true;
    }

    // Check special characters
    if (!regexSpecialChar.test(password)) {
        result['special'] = false;
    } else {
        result['special'] = true;
    }

    // Password meets all requirements
    return result;
}

/** prepare content popup */
function prepare_middle_popup(){
    $('body').addClass('pop-up');
    $('body').append('<div class="middlePopupWrapper"></div>');
    $('.middlePopupWrapper').html('<div class="middlePopup loading"><span class="close"></span><p class="title"></p><div class="content"></div></div>');
    $('.middlePopup .close').click(function(){
        $('.middlePopupWrapper').remove();
        $('body').removeClass('pop-up');
    });
    $('.middlePopupWrapper').click(function(e){
        if($(e.target).attr('class') == 'middlePopupWrapper'){
            $('.middlePopupWrapper').remove();
            $('body').removeClass('pop-up');
        }
    });
}

/** content popup render content */
function add_content_into_middle_popup(title, content){
    $('.middlePopupWrapper .middlePopup').removeClass('loading');
    $('.middlePopupWrapper .middlePopup .title').html(title);
    $('.middlePopupWrapper .middlePopup .content').html(content);
}

/** check if popup opened */
function is_popup(){
    return $('body').hasClass('pop-up');
}

/** add to cart */
function add_to_cart(el){

    const elId = el.attr("data-id");
    let flyingImg,
        flyingImgSrc;

    if(el.parent().parent().parent().hasClass('item') && el.parent().parent().parent().find(".thumbnail").length){
        flyingImg = el.parents(".item").find(".thumbnail img");
        flyingImgSrc = flyingImg.attr("src");
    } else if (el.parents(".singleCatalogItem").find(".swiper-slide.swiper-slide-active").length){
        flyingImg = el.parents(".singleCatalogItem").find(".swiper-slide.swiper-slide-active img");
        flyingImgSrc = flyingImg.attr("src");
    }

    if(!$("body").hasClass("cart-animation-in-process")){
        if(flyingImg && flyingImgSrc){
            $("body").addClass("cart-animation-in-process");
            $('<img src="'+flyingImgSrc+'" id="temp_cart_animate" style="z-index: 60; transform: translateZ(60px); position: absolute; top:'+Math.ceil(flyingImg.offset().top)+'px; left:'+Math.ceil(flyingImg.offset().left)+'px; width:'+Math.ceil(flyingImg.width())+'px; height:'+Math.ceil(flyingImg.height())+'px;">').prependTo('body');
            $('#temp_cart_animate').animate(
                {
                    top: cartIconEl.offset().top+6,
                    left: cartIconEl.offset().left+12,
                    width: 20,
                    height: 20,
                    opacity: .5,
                    borderRadius: '50%'
                }, 350,
                function() {
                    $('#temp_cart_animate').remove();
                    setTimeout(function () {
                        $("body").removeClass("cart-animation-in-process");
                    }, 200);
                }
            );
            setTimeout(function () {
                $('#temp_cart_animate').css({'z-index': '90','transform': 'translateZ(90px)'});
            }, 250);
        }
    }

    /** add cookie */
    if($.cookie("cart",  { path: '/' }) === null || $.cookie("cart",  { path: '/' }) === "") {

        /** if first cookie */
        $.cookie("cart", elId+".", {
            expires: 356,
            path: "/",
            domain: siteCookieDomain,
            secure: false
        });

    } else {

        /** if cookie exists */
        $.cookie("cart", $.cookie("cart",  { path: '/' }) + elId + ".", {
            expires: 356,
            path: "/",
            domain: siteCookieDomain,
            secure: false
        });

    }

    count_cart();

    show_cart();

}

/** fix cart array */
function clean_array(actual){
    let newArray = new Array();
    for(let i = 0; i<actual.length; i++){
        if (actual[i]){
            newArray.push(actual[i]);
        }
    }
    return newArray;
}

/** count cart icon */
function count_cart(){
    let cart = $.cookie("cart",  { path: '/' });
    if(cart && cart != "."){
        if(String(clean_array(cart.split('.')).length).replace(/ /g,'').length == 3){
            cartIconEl.find(".counter").addClass("small");
        } else {
            cartIconEl.find(".counter").removeClass("small");
        }
        cartIconEl.find(".counter").addClass("show");
        cartIconEl.find(".counter").html(clean_array(cart.split('.')).length);
    } else if(cart == ".") {
        $.cookie("cart", null, {
            expires: -1,
            path: "/",
            domain: siteCookieDomain,
            secure: false
        });
        cartIconEl.find(".counter").removeClass("small").removeClass("show").html("");
    } else {
        cartIconEl.find(".counter").removeClass("small").removeClass("show").html("");
    }
}

/** doing stuff inside positions */
function operate_positions(){

    /** remove one item from cookie */
    $(".positions .quantity .down").unbind();
    $(".positions .quantity .down").click(function() {
        $(".positions").addClass("busy");
        let newCookie = $.cookie("cart",  { path: '/' }).replace($(this).attr("data-id")+'.', '');
        $.cookie("cart", newCookie, {
            expires: 356,
            path: "/",
            domain: siteCookieDomain,
            secure: false
        });
        if(is_popup()){
            show_cart(true);
            count_cart();
        } else {
            update_checkout_positions();
        }
        return false;
    });

    /** add one item to cookie */
    $(".positions .quantity .up").unbind();
    $(".positions .quantity .up").click(function() {
        $(".positions").addClass("busy");
        let newCookie = $.cookie("cart",  { path: '/' })+$(this).attr("data-id")+'.';
        $.cookie("cart", newCookie, {
            expires: 356,
            path: "/",
            domain: siteCookieDomain,
            secure: false
        });
        if(is_popup()){
            show_cart(true);
            count_cart();
        } else {
            update_checkout_positions();
        }
        return false;
    });

    /** remove all items from */
    $(".positions .remove").unbind();
    $(".positions .remove").click(function() {
        $(".positions").addClass("busy");
        let newCookie = $.cookie("cart",  { path: '/' }).replace(new RegExp($(this).attr("data-id")+'.','g'), '');
        $.cookie("cart", newCookie, {
            expires: 356,
            path: "/",
            domain: siteCookieDomain,
            secure: false
        });
        if(is_popup()){
            show_cart(true);
            count_cart();
        } else {
            update_checkout_positions();
        }
        return false;
    });

    /** set amount manually */
    $(".positions .quantity .amount").unbind();
    $(".positions .quantity .amount").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    $(".positions .quantity .amount").unbind();
    $(".positions .quantity .amount").blur(function () {
        if($(this).val() == ""){
            $(this).val(1);
        } else if($(this).val() == "0"){
            $(this).val(1);
        } else if($(this).val() == 0){
            $(this).val(1);
        }
    });
    $(".positions .quantity .amount").unbind();
    $(".positions .quantity .amount").keyup(function (e) {

        $(".positions").addClass("busy");

        if($(this).val() == ""){
            $(this).val(1);
        } else if($(this).val() == "0"){
            $(this).val(1);
        } else if($(this).val() == 0){
            $(this).val(1);
        }

        let itemsToAdd = $(this).val();

        let newCookie = $.cookie("cart",  { path: '/' }).replace(new RegExp($(this).attr("data-id")+'.','g'), '');

        let i = 0;
        while (i < itemsToAdd) {
            newCookie += $(this).attr("data-id")+'.';
            i++;
        }

        $.cookie("cart", newCookie, {
            expires: 356,
            path: "/",
            domain: siteCookieDomain,
            secure: false
        });

        if(is_popup()){
            show_cart(true);
            count_cart();
        } else {
            update_checkout_positions();
        }

    });

}

/** render cart */
function show_cart(update){

    if(!update){
        prepare_middle_popup();
    }

    $.ajax({
        type: "POST",
        url: ajaxUrl,
        dataType: "json",
        cache: false,
        data: {
            "action": "show_cart"
        },
        success : function (out) {
            if(out.status == "ok"){

                add_content_into_middle_popup(out.title, out.content);

                $('.positions').removeClass('busy');

                operate_positions();

            }
        }
    });

}

/** update checkout positions */
function update_checkout_positions(){
    $.ajax({
        type: "POST",
        url: ajaxUrl,
        dataType: "json",
        cache: false,
        data: {
            "action": "update_checkout_positions"
        },
        success : function (out) {
            if(out.status == "ok"){
                $('.positionsWrapper').html(out.html);
                $('.positionsWrapper .positions').removeClass('busy');
                operate_positions();
                if($('.npWrapper').length){
                    if($('#citySearch').val()){
                        $.ajax({
                            type: "POST",
                            url: ajaxUrl,
                            dataType: "json",
                            cache: false,
                            data: {
                                "action": "np_get_offices_by_city_ref",
                                "ref": $('#citySearch').val(),
                            },
                            success : function (out) {
                                if(out[0]?.text){
                                    $('input[name="user_np_office_name"]').val(out[0].text);
                                }
                                $('#postOfficeNumberSearch').val(null);
                                $('#postOfficeNumberSearch').empty();
                                $('#postOfficeNumberSearch').trigger('change');
                                $('#postOfficeNumberSearch').select2({
                                    placeholder: "Оберіть відділення",
                                    language: {
                                        noResults: function() {
                                            return "Відділення не знайдено";
                                        }
                                    },
                                });
                                $('#postOfficeNumberSearch').prop('disabled', false).trigger('change.select2');

                                if(out.length > 0){
                                    $('#postOfficeNumberSearch').select2({
                                        data: out
                                    });
                                    $('#postOfficeNumberSearch').prop('disabled', false).trigger('change.select2');
                                } else {
                                    $('#postOfficeNumberSearch').select2({
                                        placeholder: "Відділення не знайдено"
                                    });
                                    $('#postOfficeNumberSearch').prop('disabled', true).trigger('change.select2');
                                }
                            }
                        });
                    }
                }
            } else if(out.status == "empty"){
                $('.orderColumns').removeClass('show');
                $('.noOrder').addClass('show');
            }
        }
    });
}

/** google maps */
async function map_init(mapEl, position, contentString) {
    const { Map } = await google.maps.importLibrary("maps");
    let map = new Map(mapEl[0], {
        center: position,
        zoom: 14,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: true,
        scaleControl: true,
        zoomControl: true,
        scrollwheel: false,
        styles: mapStyle
    });
    let image = {
        url: mapDotUrl,
        size: new google.maps.Size(33, 48),
        scaledSize: new google.maps.Size(33, 48),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(16, 48)
    };
    let marker = new google.maps.Marker({
        position: position,
        map: map,
        icon: image,
        title: contentString
    });
    marker.addListener('click', function() {
        const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${position.lat},${position.lng}`;
        window.open(googleMapsUrl, '_blank'); // Opens Google Maps in a new window or tab with the marker's location
    });
}
