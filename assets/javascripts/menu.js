import axios from "axios";
import "../styles/menu.css";
import { formatDate } from "./global.js";

//INITIALISATION VARIABLE
const btnValider = document.getElementById("btnValider");
const btnReset = document.getElementById("btnReset");
const semaine = document.getElementById("semaine");
const errorMsg = document.getElementById("errorMsg");
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

//FORM CONTROL

function formControl(event) {
  callValid(validateForm(), event);
}

// function to add the error message
function callValid(formValid, event) {
  var errorMsg = document.getElementById("errorMsg");
  if (!formValid) {
    errorMsg.innerHTML =
      "Tous les champs d'un jour non coché doivent étre remplis.";
    errorMsg.style.display = "block";
    event.preventDefault();
  }
}

//Function to Make the error msg dissapear

function resetStyle() {
  errorMsg.style.display = "none";
  errorMsg.innerHTML = "";
}

//Validator

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

//CALL

btnValider.addEventListener("click", formControl);
semaine.addEventListener("change", function () {
  resetStyle();
});
btnReset.addEventListener("click", resetStyle);
