/**
 * variables
 */

const visibleClass = "show";
const animateElements = ['.scroll-show'];
const isMobile = navigator.userAgent.match(/Mobile/i) == "Mobile";
const ajaxUrl = "/wp-admin/admin-ajax.php";
const siteCookieDomain = "."+document.location.hostname.replace("www.","");
const footerEl = document.querySelector('footer');
const bodyEl = document.querySelector('body');
let elementsFadeInTimer = 30;
let cartIconEl;
