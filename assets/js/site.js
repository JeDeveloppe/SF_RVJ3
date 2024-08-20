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

//header effect
let scrollFromTop = document.documentElement.scrollTop;

document.addEventListener("scroll", scrollFunction);

function scrollFunction() {
  let currentScroll = document.documentElement.scrollTop;
  let nav_header = document.getElementById("nav_header");
  let pre_header = document.getElementById("pre_header");

  if(currentScroll < 50) {

    nav_header.classList.remove("py-0");
    nav_header.classList.add("py-3");

  }else{

    nav_header.classList.remove("py-3");
    nav_header.classList.add("py-0");

  }
}

// End Header effect