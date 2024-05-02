import axios from "axios";
import "../styles/reservation.css";
import { formatDate } from "./global.js";

// Initialisation
var btnValider = document.getElementById("btnValider");
var isEventListenerAdded = false;
const form = document.querySelector("form");
var choices = document.querySelectorAll('input[type="checkbox"]');
var weekday = ["Monday", "Tuesday", "Wednesday", "Thursday"];

// Fetch Semaine pour option select
axios
  .get("/user/reservation/semaineJson")
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
    })
    .finally(function () {
      // dans tous les cas
    });
}

function insertRepas(data) {
  const dateJour = new Date();
  const dateLimit = new Date(data.dateLimit);
  const options = { timeZone: "Indian/Reunion" };
  const formatter = new Intl.DateTimeFormat("fr-Fr", options);
  const dateSub = subtractDays(dateLimit, 1);
  const dateRéu = formatter.format(dateSub);
  const dateContainer = document.getElementById("dateLimit");
  dateContainer.innerHTML = dateRéu;

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
  const jour = data.jourReservation;

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
      const userJs = document.querySelector(".js-user");
      const userTarif = userJs.getAttribute("data-tarif");
      let tarif;

      if (userTarif) {
        tarif = repas[j].typeRepas.tarifReduit;
      } else {
        tarif = repas[j].typeRepas.tarifPlein;
      }

      const description = repas[j].description;
      const formattedText =
        description.replace(/\n/g, "<br>") +
        `<br><span class='prix'>${tarif} €</span>`;

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
  var repasContainer = document.getElementsByClassName("repasContainer");
  var ferieContainer = document.getElementsByClassName("ferie");
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
