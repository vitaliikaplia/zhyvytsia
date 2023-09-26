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
    let swiperSlider = new Swiper(element, {
        // Optional parameters
        direction: 'horizontal',
        slidesPerView: 4,
        spaceBetween: 40,
        loop: true,
        pagination: {
            el: ".feedback-swiper-pagination",
            clickable: true
        },
        // Navigation arrows
        // navigation: {
        //     nextEl: '.areas-swiper-button-next',
        //     prevEl: '.areas-swiper-button-prev',
        // },
        grabCursor: true
    });
}

/** про нас */
function run_about_slider(element){
    let swiperSlider = new Swiper(element, {
        // Optional parameters
        direction: 'horizontal',
        slidesPerView: 8,
        spaceBetween: 0,
        loop: true,
        // Navigation arrows
        // navigation: {
        //     nextEl: '.areas-swiper-button-next',
        //     prevEl: '.areas-swiper-button-prev',
        // },
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
                            thisFormEl.removeClass("busy");
                            thisFormEl.trigger("reset");
                        }
                    }
                });

            }

        }

    });

}
