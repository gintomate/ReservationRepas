import axios from "axios";
import "../styles/reservation.css";

const userJs = document.querySelector(".js-user");
const userTarif = userJs.getAttribute("data-tarif");
const semaine = userJs.getAttribute("data-semaine");
fetchMenu(semaine);

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

function insertRepas(data) {
  const jour = data.semaine.jourReservation;
  const reservation = data.reservation;

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
          dayArray[1].innerHTML = formattedText;
          var checkbox = dayArray[1].parentNode.querySelector(
            ' input[type="radio"]'
          );
          if (reserve === true) {
            checkbox.checked = true;
          }
          break;
        case "déjeuner_b":
          dayArray[2].innerHTML = formattedText;
          var checkbox = dayArray[2].parentNode.querySelector(
            ' input[type="radio"]'
          );
          if (reserve === true) {
            checkbox.checked = true;
          }
          break;
        case "diner":
          dayArray[3].innerHTML = formattedText;
          var checkbox = dayArray[3].parentNode.querySelector(
            'input[type="checkbox"]'
          );
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

function getDayClass(day) {
  var dayClass = document.getElementsByClassName(day);
  const dayArray = Array.from(dayClass);
  return dayArray;
}

// RESET THE STYLE ON CHANGE
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
  var input = document.querySelectorAll(
    'input[type="checkbox"], input[type="radio"]'
  );

  // Loop through each checkbox and uncheck it
  input.forEach(function (checkbox) {
    checkbox.checked = false;
  });
  calculatePrice();
}

//afficher total avec JS

var choices = document.querySelectorAll(
  'input[type="checkbox"], input[type="radio"]'
);
choices.forEach(function (input) {
  input.addEventListener("click", calculatePrice);
});

function calculatePrice() {
  var caseTotal = document.getElementById("caseTotal");
  var total = 0;
  //When unchecked the checkbox disapear from the list
  var checkboxes = document.querySelectorAll(
    'input[type="checkbox"]:checked, input[type=radio]:checked'
  );

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

const form = document.querySelector("form");
form.addEventListener("submit", callValid);

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

function callValid(event) {
  var errorMsg = document.getElementById("errorMsg");
  if (!validateForm()) {
    // If form is not valid, prevent the default form submission
    event.preventDefault();
    errorMsg.innerHTML = "Veuillez cocher au moins une case.";
    errorMsg.classList.add("alert");
    return false; // Ensure the form submission is blocked
  }
}

//remove error message
var btnReset = document.getElementById("btnReset");
btnReset.addEventListener("click", resetError);
var errorMsg = document.getElementById("errorMsg");

function resetError() {
  errorMsg.classList.remove("alert");
  errorMsg.innerHTML = "";
}
