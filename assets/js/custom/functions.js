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
