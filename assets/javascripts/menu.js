import axios from "axios";
import "../styles/menu.css";

// Requêter pour chercher Semaine Json.
axios
  .get("/admin/menu/creerJson")
  .then(function (response) {
    insertOption(response.data);
  })
  .catch(function (error) {
    // en cas d’échec de la requête
    console.log(error);
  })
  .finally(function () {
    // dans tous les cas
  });

// Requêter pour inserer Semaine Select.
function insertOption(data) {
  var semaine = document.getElementById("semaine");
  semaine.innerHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];
    var semaineId = item.id;

    // Parse date strings to Date objects
    var dateDebut = new Date(item.dateDebut);
    var dateFin = new Date(item.dateFin);

    // Format date components to d-m-Y format
    var formattedDateDebut = formatDate(dateDebut);
    var formattedDateFin = formatDate(dateFin);

    // Create an option element and set its text content
    const option = document.createElement("option");
    option.textContent =
      item.numeroSemaine + ": " + formattedDateDebut + " - " + formattedDateFin;
    option.value = semaineId;

    // Append the option to the select element
    semaine.appendChild(option);
  }
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
    errorMsg.classList.add("alert");
    event.preventDefault();
  }
}

//remove error message
var btnReset = document.getElementById("btnReset");
btnReset.addEventListener("click", resetStyle);
var errorMsg = document.getElementById("errorMsg");
function resetStyle() {
  errorMsg.classList.remove("alert");
  errorMsg.innerHTML = "";
}

semaine.addEventListener("change", function () {
  resetStyle();
});
