/** window load scroll resize */
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
    const windowWidth = window.innerWidth || document.documentElement.clientWidth;

    const elementTopVisible = rect.top >= 0 && rect.top <= windowHeight;
    const elementBottomVisible = rect.bottom >= 0 && rect.bottom <= windowHeight;
    const elementLeftVisible = rect.left >= 0 && rect.left <= windowWidth;
    const elementRightVisible = rect.right >= 0 && rect.right <= windowWidth;

    return (
        (elementTopVisible || elementBottomVisible) &&
        (elementLeftVisible || elementRightVisible)
    );
}

function fadeInElement(element) {
    if (!element.classList.contains(visibleClass)) {
        setTimeout(function () {
            element.classList.add(visibleClass);
        }, elementsFadeInTimer);
        elementsFadeInTimer += 90;
    }
}

function checkElementsVisibility() {
    animateElements.forEach(function (value) {
        const elements = document.querySelectorAll(value);
        elements.forEach(function (element) {
            if (isInViewport(element) || isMobile) {
                fadeInElement(element);
            }
        });
    });

    if (elementsFadeInTimer > 30) {
        elementsFadeInTimer = 30;
    }
}

window.addEventListener("DOMContentLoaded", checkElementsVisibility);
window.addEventListener("load", checkElementsVisibility);
window.addEventListener("scroll", checkElementsVisibility);
window.addEventListener("resize", checkElementsVisibility);
