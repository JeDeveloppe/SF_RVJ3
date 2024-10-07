/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// import '../styles/scss/template_bootstrap.scss';
import '../styles/css/cookieconsent.css';
import '../styles/scss/site.scss';
import '../styles/css/animations.css';
import './toast';
import './bootstrap.js';

require('bootstrap');


// SEO skill
document.querySelector('meta[property~="og:title"]').setAttribute("content",document.title);
document.querySelector('meta[name="description"]').setAttribute("content",document.querySelector('meta[property~="og:description"]').content);
// End SEO skill

// CookieBand effect 
let cookieChecked = getCookie('Refaitesvosjeux');

if(cookieChecked !== "cookieChecked"){
  let cookieHeadBand = document.querySelector("#overlay");
  let cookieHeadBandButton = document.querySelector("#cookieHeadBandButton");
  cookieHeadBand.style.display="block";

  cookieHeadBandButton.addEventListener('click', () => {
    cookieHeadBand.style.display="none";
    setCookie('Refaitesvosjeux','cookieChecked');
  })
}

function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for(let i = 0; i <ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
// End CookieBand effect 


//menu effect for display or not menu user when collapsed
let menu_user_cart = document.getElementById('menu_user_cart');
let navbar_button = document.getElementById('navbar_button');
let logoNav = document.getElementById('logoNav');

navbar_button.addEventListener('click', displayMenuUserCart);

function displayMenuUserCart(){
    if(navbar_button.getAttribute('aria-expanded') === "true"){
      menu_user_cart.style.opacity = 0;
      logoNav.style.opacity = 0;
    }else{
      menu_user_cart.style.opacity = 1;
      logoNav.style.opacity = 1;
    }
}
//menu effect for display or not menu user when collapsed