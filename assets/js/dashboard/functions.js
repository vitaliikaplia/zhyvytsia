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
