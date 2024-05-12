import axios from "axios";
import "../styles/reservation.css";
import { formatDate } from "./global.js";

// Constants
const weekday = ["Monday", "Tuesday", "Wednesday", "Thursday"];

// DOM Elements
const btnValider = document.getElementById("btnValider");
const form = document.querySelector("form");
const choices = document.querySelectorAll('input[type="checkbox"]');
const dateContainer = document.getElementById("dateLimit");
const repasContainer = document.getElementsByClassName("repasContainer");
const ferieContainer = document.getElementsByClassName("ferie");
let isEventListenerAdded = false;

// Fetch Semaine pour option select
function fetchSemaine() {
  axios
    .get("/user/reservation/semaineJson")
    .then(function (response) {
      insertOption(response.data);
    })
    .catch(function (error) {
      // en cas d’échec de la requête
      console.log(error);
    });
}
//Insert Select
function insertOption(data) {
  var semaine = document.getElementById("semaine");
  semaine.innerHTML = "";
  var optionsHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];
    var semaineId = item.id;
    // Parse date strings to Date objects
    optionsHTML += `<option value="${semaineId}">${
      item.numeroSemaine
    }: ${formatDate(new Date(item.dateDebut))} - ${formatDate(
      new Date(item.dateFin)
    )}</option>`;

    // Append the option to the select element
    semaine.innerHTML = optionsHTML;
  }
  var optionPassed = semaine.options[0].value;
  fetchMenu(optionPassed);
}

// function to fetch the menu
function fetchMenu(value) {
  axios
    .get("/user/reservation/repasJson/" + value)
    .then(function (response) {
      insertRepas(response.data);
    })
    .catch(function (error) {
      // en cas d’échec de la requête
      console.log(error);
    });
}

function insertRepas(data) {
  // Definit la date du jour et la date limite
  const dateJour = new Date();
  const dateLimit = new Date(data.dateLimit);
  const options = { timeZone: "Indian/Reunion" };
  const formatter = new Intl.DateTimeFormat("fr-FR", options);
  const dateSub = subtractDays(dateLimit, 1);
  const dateRéu = formatter.format(dateSub);
  dateContainer.innerHTML = dateRéu;

  //Definit si la réservation est ouverte
  const isReservationClosed = dateJour >= dateLimit;

  // Add event listener only if reservation is closed and listener not added
  if (isReservationClosed && !isEventListenerAdded) {
    btnValider.addEventListener("click", preventDefaultOnClick, false);
    isEventListenerAdded = true;
  } else if (!isReservationClosed && isEventListenerAdded) {
    btnValider.removeEventListener("click", preventDefaultOnClick);
    isEventListenerAdded = false;
  }

  // Display error message if reservation is closed
  if (isReservationClosed) {
    errorMsg.textContent = "Réservation Terminée.";
    errorMsg.classList.remove("alert");
  }

  const jours = data.jourReservation;

  for (const jour of jours) {
    const date = new Date(jour.dateJour);
    const jourIndex = date.toLocaleDateString("en-EN", { weekday: "long" });
    //Affiche spécial pour jour sans repas
    if (jour.ferie) {
      hideAndShowElements(jourIndex, true);
      continue;
    }
    const repas = jour.repas;
    //retourne toutes les div du jour
    const dayArray = getDayClass(jourIndex);
    //Insere le repas dans le menu
    for (const repasItem of repas) {
      const type = repasItem.typeRepas.type;
      const userJs = document.querySelector(".js-user");
      const userTarif = userJs.getAttribute("data-tarif");
      const tarif = userTarif
        ? repasItem.typeRepas.tarifReduit
        : repasItem.typeRepas.tarifPlein;
      const description = repasItem.description;
      const formattedText =
        description.replace(/\n/g, "<br>") +
        `<br><span class='prix'>${tarif} €</span>`;
      //Insertion par type
      switch (type) {
        case "petit_déjeuner":
          dayArray[0].innerHTML = formattedText;
          break;
        case "déjeuner_a":
          dayArray[1].innerHTML = formattedText;
          break;
        case "déjeuner_b":
          dayArray[2].innerHTML = formattedText;
          break;
        case "diner":
          dayArray[3].innerHTML = formattedText;
          break;
        default:
          break;
      }
    }
  }
}

function hideAndShowElements(dayIndex, isHoliday) {
  const repasHidden = repasContainer[dayIndex + "Repas"];
  const ferie = ferieContainer[dayIndex];
  ferie.classList.toggle("hidden", !isHoliday);
  repasHidden.classList.toggle("hidden", isHoliday);
}

// Function to make checkbox behave like Radio

weekday.forEach((day) => {
  var checkboxes = document.querySelectorAll(
    'input[type="checkbox"][name="day[' + day + '][dejeuner]"]'
  );
  checkboxes.forEach(function (checkbox) {
    checkbox.addEventListener("change", function () {
      if (this.checked) {
        checkboxes.forEach(function (cb) {
          if (cb !== checkbox) {
            cb.checked = false;
          }
        });
      }
    });
  });
});

//FUNCTION to PREVENT DEFAULT

function preventDefaultOnClick(event) {
  event.preventDefault();
}

//FUNCTION TO SUBSTRACT THE DAY TO SHOW THE LAST DAY OF THE RESERVATION

function subtractDays(date, days) {
  var result = new Date(date);
  result.setDate(result.getDate() - days);
  return result;
}

//FUNCTION TO RETURN AN ARRAY OF EVERY INPUT

function getDayClass(day) {
  var dayClass = document.getElementsByClassName(day);
  const dayArray = Array.from(dayClass);
  return dayArray;
}

// RESET THE SYLE ON CHANGE

function resetStyles() {
  var repasArray = Array.from(repasContainer);
  var ferieArray = Array.from(ferieContainer);

  repasArray.forEach(function (element) {
    // Remove the 'hidden' class if present
    element.classList.remove("hidden");
  });
  ferieArray.forEach(function (element) {
    // Remove the 'hidden' class if present
    element.classList.add("hidden");
  });
  var input = document.querySelectorAll('input[type="checkbox"]');

  // Loop through each checkbox and uncheck it
  input.forEach(function (checkbox) {
    checkbox.checked = false;
  });
  calculatePrice();
}

//afficher total avec JS

function calculatePrice() {
  var caseTotal = document.getElementById("caseTotal");
  var total = 0;
  //When unchecked the checkbox disapear from the list
  var checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');

  checkboxes.forEach(function (input) {
    var associatedPrice = input.parentNode.querySelector("p span");
    var prixSeul = parseFloat(associatedPrice.textContent);

    if (!isNaN(prixSeul)) {
      total += prixSeul;
    }
  });
  caseTotal.value = total.toFixed(2); // Set total value to 2 decimal places
}

// FORM CONTROL
function callValid(event) {
  var errorMsg = document.getElementById("errorMsg");
  if (!validateForm()) {
    // If form is not valid, prevent the default form submission
    event.preventDefault();
    errorMsg.innerHTML = "Veuillez cocher au moins une case.";
    errorMsg.classList.remove("alert");
    return false; // Ensure the form submission is blocked
  }
}

//Validator
function validateForm() {
  const checkboxes = document.querySelectorAll('input[type="checkbox"]');
  var validForm = false;
  var checkboxCheck = false;
  checkboxes.forEach(function (input) {
    if (input.type === "checkbox" && input.checked) {
      checkboxCheck = true;
    }
  });
  if (checkboxCheck) {
    validForm = true;
  }
  return validForm;
}

//FUNCTION TO Hide/SHOW THE ERROR MSG

function resetError() {
  var errorMsgC = document.getElementsByClassName("errorMsg");
  for (var i = 0; i < errorMsgC.length; i++) {
    errorMsgC[i].classList.add("alert");
    errorMsgC[i].innerHTML = "";
  }
}

//call select change

window.addEventListener("DOMContentLoaded", () => {
  fetchSemaine();
  semaine.addEventListener("change", function () {
    var selectedOption = this.options[this.selectedIndex];
    var selectedOptionValue = selectedOption.value;
    resetStyles();
    resetError();
    fetchMenu(selectedOptionValue);
  });
  //call check price change
  choices.forEach(function (input) {
    input.addEventListener("click", calculatePrice);
  });
  //call form control
  form.addEventListener("submit", callValid);
});
