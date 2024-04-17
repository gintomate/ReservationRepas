import axios from "axios";
import "../styles/menu.css";

// Requêter pour chercher Semaine Json.
axios
  .get("/admin/menu/creerJson")
  .then(function (response) {
    insertOption(response.data);
  })
  .catch(function (error) {
    console.log(error);
  })
  .finally(function () {
    // dans tous les cas
  });

// Requêter pour inserer Semaine Select.
function insertOption(data) {
  var semaine = document.getElementById("semaine");
  semaine.innerHTML = "";
  var optionsHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];
    var semaineId = item.id;
    optionsHTML += `<option value="${semaineId}">${
      item.numeroSemaine
    }: ${formatDate(new Date(item.dateDebut))} - ${formatDate(
      new Date(item.dateFin)
    )}</option>`;
  }
  // Append the option to the select element
  semaine.innerHTML = optionsHTML;
}

// Function to format a Date object to d-m-Y format
function formatDate(date) {
  var day = date.getDate();
  var month = date.getMonth() + 1; // Months are zero-based
  var year = date.getFullYear();

  // Ensure leading zeros for day and month if necessary
  day = day < 10 ? "0" + day : day;
  month = month < 10 ? "0" + month : month;

  // Return the formatted date string
  return day + "-" + month + "-" + year;
}

//call to validate the form

var btnValider = document.getElementById("btnValider");
btnValider.addEventListener("click", formControl);

function formControl(event) {
  callValid(validateForm(), event);
}

function validateForm() {
  const days = ["lundi", "mardi", "mercredi", "jeudi", "vendredi"];
  for (const day of days) {
    const checkboxes = document.getElementsByClassName(day);
    const checkboxesArray = Array.from(checkboxes);

    if (!checkboxes[0].checked) {
      const emptyFields = checkboxesArray.some((repas) => !repas.value);
      if (emptyFields) return false;
    }
  }
  return true;
}

// function to add the error message
function callValid(formValid, event) {
  var errorMsg = document.getElementById("errorMsg");
  if (!formValid) {
    errorMsg.innerHTML = "Tous les champs non férié doivent étre remplis.";
    errorMsg.style.display = "block";
    event.preventDefault();
  }
}

//remove error message
var btnReset = document.getElementById("btnReset");
btnReset.addEventListener("click", resetStyle);
var errorMsg = document.getElementById("errorMsg");
function resetStyle() {
  errorMsg.style.display = "none";
  errorMsg.innerHTML = "";
}

semaine.addEventListener("change", function () {
  resetStyle();
});
