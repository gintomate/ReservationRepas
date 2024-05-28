import axios from "axios";
import "../styles/reservation.css";
import { formatDate } from "./global.js";

// Initialisation
var btnValider = document.getElementById("btnValider");
const userJs = document.querySelector(".js-user");
const userTarif = userJs.getAttribute("data-tarif");
const semaine = userJs.getAttribute("data-semaine");
const form = document.querySelector("form");
var isEventListenerAdded = false;
var choices = document.querySelectorAll('input[type="checkbox"]');
var weekday = ["Monday", "Tuesday", "Wednesday", "Thursday"];

document.addEventListener("DOMContentLoaded", () => {
  fetchMenu(semaine);
});

function fetchMenu(value) {
  axios
    .get("/user/reservation/repasModifJson/" + value)
    .then(function (response) {
      insertSemaine(response.data);
      insertRepas(response.data);
      calculatePrice();
    })
    .catch(function (error) {
      // en cas d’échec de la requête
      console.log(error);
    })
    .finally(function () {
      // dans tous les cas
    });
}
function insertSemaine(data) {
  var semaine = data.semaine;
  var week = document.getElementById("week");
  var dateDebut = new Date(semaine.dateDebut);
  var dateFin = new Date(semaine.dateFin);

  week.textContent = "";
  // Format date components to d-m-Y format
  var formattedDateDebut = formatDate(dateDebut);
  var formattedDateFin = formatDate(dateFin);
  week.textContent =
    semaine.numeroSemaine +
    " : " +
    formattedDateDebut +
    " - " +
    formattedDateFin;
}

function insertRepas(data) {
  const dateJour = new Date();
  const dateLimit = new Date(data.semaine.dateLimit);
  const options = { timeZone: "Indian/Reunion" };
  const formatter = new Intl.DateTimeFormat("fr-Fr", options);
  const dateSub = subtractDays(dateLimit, 1);
  const dateRéu = formatter.format(dateSub);
  const dateContainer = document.getElementById("dateLimit");
  dateContainer.innerHTML = dateRéu;
  const jour = data.semaine.jourReservation;
  const reservation = data.reservation;

  const isReservationClosed = dateJour >= dateLimit;

  // Check the condition and add or remove the event listener accordingly
  if (isReservationClosed && !isEventListenerAdded) {
    btnValider.addEventListener("click", preventDefaultOnClick, false);
    isEventListenerAdded = true; // Set flag to true as listener is added
  } else if (!isReservationClosed && isEventListenerAdded) {
    btnValider.removeEventListener("click", preventDefaultOnClick);
    isEventListenerAdded = false; // Set flag to false as listener is removed
  }
  // Check the condition and add or remove the event listener accordingly
  if (dateJour >= dateLimit) {
    errorMsg.innerHTML = "Réservation Terminé.";
    errorMsg.classList.remove("alert");
  }

  for (let i = 0; i < jour.length; i++) {
    const date = new Date(jour[i].dateJour);
    const options = { weekday: "long" };
    const jourIndex = date.toLocaleDateString("en-EN", options);

    if (jour[i].ferie === true) {
      var repasContainer = document.getElementsByClassName("repasContainer");
      var repasHidden = repasContainer[jourIndex + "Repas"];
      var ferieContainer = document.getElementsByClassName("ferie");
      var ferie = ferieContainer[jourIndex];
      ferie.classList.remove("hidden");
      repasHidden.classList.add("hidden");
      continue; // Skip further processing if it's a holiday
    }

    const repas = jour[i].repas;
    const dayArray = getDayClass(jourIndex);

    for (let j = 0; j < repas.length; j++) {
      const type = repas[j].typeRepas.type;
      const repasReserves = reservation.repasReserves;
      let tarif;

      if (userTarif) {
        tarif = repas[j].typeRepas.tarifReduit;
      } else {
        tarif = repas[j].typeRepas.tarifPlein;
      }
      var reserve = false;
      for (let k = 0; k < repasReserves.length; k++) {
        var repasResId = repasReserves[k].repas.id;
        if (repasResId === repas[j].id) {
          reserve = true;
          break;
        }
      }

      const description = repas[j].description;
      const formattedText =
        description.replace(/\n/g, "<br>") +
        `<br><span class='prix'>${tarif} €</span>`;

      switch (type) {
        case "petit_déjeuner":
          var checkbox = dayArray[0].parentNode.querySelector(
            'input[type="checkbox"]'
          );
          dayArray[0].innerHTML = formattedText;
          if (reserve === true) {
            checkbox.checked = true;
          }
          break;
        case "déjeuner_a":
          var checkbox = dayArray[1].parentNode.querySelector(
            'input[type="checkbox"]'
          );
          dayArray[1].innerHTML = formattedText;
          if (reserve === true) {
            checkbox.checked = true;
          }
          break;
        case "déjeuner_b":
          var checkbox = dayArray[2].parentNode.querySelector(
            'input[type="checkbox"]'
          );
          dayArray[2].innerHTML = formattedText;
          if (reserve === true) {
            checkbox.checked = true;
          }
          break;
        case "diner":
          var checkbox = dayArray[3].parentNode.querySelector(
            'input[type="checkbox"]'
          );
          dayArray[3].innerHTML = formattedText;
          if (reserve === true) {
            checkbox.checked = true;
          }
          break;
        default:
          break;
      }
    }
  }
}

// Function to make checkbox behave like Radio

weekday.forEach((day) => {
  var checkboxes = document.querySelectorAll(
    'input[type="checkbox"][class="radio' + day + '"]'
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

// Function to calculate the price of the Menu

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
  const checkboxes = document.querySelectorAll(
    'input[type="checkbox"], input[type="radio"]'
  );
  var validForm = false;
  var checkboxCheck = false;
  var radioChecked = false;
  checkboxes.forEach(function (input) {
    if (input.type === "checkbox" && input.checked) {
      checkboxCheck = true;
    }
    if (input.type === "radio" && input.checked) {
      radioChecked = true;
    }
  });
  if (radioChecked || checkboxCheck) {
    validForm = true;
  }
  return validForm;
}

function preventDefaultOnClick(event) {
  event.preventDefault();
}

//afficher total avec JS
choices.forEach(function (input) {
  input.addEventListener("click", calculatePrice);
});
form.addEventListener("submit", callValid);
