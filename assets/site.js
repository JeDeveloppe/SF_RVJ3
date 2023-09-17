/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/template_bootstrap.scss';
import './styles/site.scss';
import './toast';

require('bootstrap');

window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop >= 80 || document.documentElement.scrollTop >= 80) {
    document.getElementById("navbar-top").style.padding = "5px 2px";
    document.getElementById("navbar-top").classList.remove("mt-2");
    document.getElementById("logo").style.fontSize = "25px";
  } else {
    document.getElementById("navbar-top").style.padding = "30px 10px";
    document.getElementById("navbar-top").classList.add("mt-2");
    document.getElementById("logo").style.fontSize = "35px";
  }
}