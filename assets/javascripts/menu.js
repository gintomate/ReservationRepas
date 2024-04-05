import axios from "axios";
import "../styles/menu.css";

// Requêter pour inseree Semaine.
axios
  .get("/menu/creer/get")
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

function insertOption(data) {
  var semaine = document.getElementById("semaine");
  semaine.innerHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];

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

function formControl() {
  validateForm();
  callValid(validateForm());
}

//function to validate the form
function validateForm() {
  var formValid = true;
  var lundi = document.getElementsByClassName("lundi");
  const lundiArray = Array.from(lundi);
  var mardi = document.getElementsByClassName("mardi");
  const mardiArray = Array.from(mardi);
  var mercredi = document.getElementsByClassName("mercredi");
  const mercrediArray = Array.from(mercredi);
  var jeudi = document.getElementsByClassName("jeudi");
  const jeudiArray = Array.from(jeudi);
  var vendredi = document.getElementsByClassName("vendredi");
  const vendrediArray = Array.from(vendredi);

  if (!lundi[0].checked) {
    lundiArray.forEach((repas) => {
      if (!repas.value) {
        formValid = false;
      }
    });
  }
  if (!mardi[0].checked) {
    mardiArray.forEach((repas) => {
      if (!repas.value) {
        formValid = false;
      }
    });
  }
  if (!mercredi[0].checked) {
    mercrediArray.forEach((repas) => {
      if (!repas.value) {
        formValid = false;
      }
    });
  }
  if (!jeudi[0].checked) {
    jeudiArray.forEach((repas) => {
      if (!repas.value) {
        formValid = false;
      }
    });
  }
  if (!vendredi[0].checked) {
    vendrediArray.forEach((repas) => {
      if (!repas.value) {
        formValid = false;
      }
    });
  }
  return formValid;
}

// function to add the error message
function callValid(formValid) {
  var errorMsg = document.getElementById("errorMsg");
  if (!formValid) {
    errorMsg.innerHTML = "Tous les champs non férié doivent étre remplis.";
    errorMsg.classList.add("alert");
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
