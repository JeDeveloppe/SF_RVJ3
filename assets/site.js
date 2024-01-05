/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/cookieconsent.css';
import './styles/template_bootstrap.scss';
import './styles/site.scss';
import './toast';
import './bootstrap.js';

require('bootstrap');

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