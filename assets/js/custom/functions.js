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
        // Navigation arrows
        navigation: {
            nextEl: '.areas-swiper-button-next',
            prevEl: '.areas-swiper-button-prev',
        },
        grabCursor: true
    });
}
