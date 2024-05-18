/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../styles/css/cookieconsent.css';
import '../styles/scss/template_bootstrap.scss';
import '../styles/scss/site.scss';
import '../styles/css/animations.css';
import './toast';
import './bootstrap.js';

require('bootstrap');

// Navbar effect
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop >= 80 || document.documentElement.scrollTop >= 80) {
    document.getElementById("navbar-top").style.padding = "5px 2px";
    document.getElementById("navbar-top").classList.remove("mt-3");
    document.getElementById("logoTop").style.scale = 0.5;
  } else {
    document.getElementById("navbar-top").style.padding = "30px 10px";
    document.getElementById("navbar-top").classList.add("mt-3");
    document.getElementById("logoTop").style.scale = 1;
  }
}
// End navbar effect

// SEO skill
document.querySelector('meta[property~="og:title"]').setAttribute("content",document.title);
document.querySelector('meta[name="description"]').setAttribute("content",document.querySelector('meta[property~="og:description"]').content);
// End SEO skill

// CookieBand effect 
let cookieChecked = getCookie('Refaitesvosjeux');

if(cookieChecked !== "cookieChecked"){
  let cookieHeadBand = document.querySelector(".cookiealert");
  let cookieHeadBandButton = document.querySelector("#cookieHeadBandButton");
  cookieHeadBand.classList.add('show');

  cookieHeadBandButton.addEventListener('click', () => {
    cookieHeadBand.classList.remove('show');
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

let links = Array.from(document.querySelectorAll('.detect-rotate'));
links.forEach((arrayElement, index) => {
  setTimeout(function(){
    links[index].firstElementChild.classList.add('opacity-0');
 }, 20);
});

links.forEach((link) => link.addEventListener(
    "mouseover",
    function (link) {
      // on met l'accent sur la cible de mouseover
      link.target.firstElementChild.classList.remove("opacity-0");
      link.target.firstElementChild.classList.add("roll-in-left");

      setTimeout(function () {
        link.target.firstElementChild.classList.add("opacity-0");
        link.target.firstElementChild.classList.remove("roll-in-left");
      }, 5000);
    },
    false,
  )
)