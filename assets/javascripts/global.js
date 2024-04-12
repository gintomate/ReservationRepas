/* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */

var navBtn = document.getElementById("navBtn");
navBtn.addEventListener("click", showNav);

function showNav() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
