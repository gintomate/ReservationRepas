import axios from "axios";
import "../styles/reservation.css";

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

//function to change on select
semaine.addEventListener("change", function () {
  var selectedOption = this.options[this.selectedIndex];
  var selectedOptionValue = selectedOption.value;
  resetStyles();
  resetError();
  fetchMenu(selectedOptionValue);
});

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
var isEventListenerAdded = false;
var btnValider = document.getElementById("btnValider");
function insertRepas(data) {
  var dateJour = new Date();
  const dateLimit = new Date(data.dateLimit);
  const options = { timeZone: "Indian/Reunion" };
  const formatter = new Intl.DateTimeFormat("fr-Fr", options);
  const dateSub = subtractDays(dateLimit, 1);
  const dateRéu = formatter.format(dateSub);
  const dateContainer = document.getElementById("dateLimit");
  dateContainer.innerHTML = dateRéu;

  // Check the condition and add or remove the event listener accordingly
  if (dateJour >= dateLimit && !isEventListenerAdded) {
    btnValider.addEventListener("click", preventDefaultOnClick, false);
    isEventListenerAdded = true; // Set flag to true as listener is added
  } else if (dateJour < dateLimit && isEventListenerAdded) {
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
    errorMsg.classList.remove("alert");
    return false; // Ensure the form submission is blocked
  }
}

// ON RESET CLEAN AND START OVER

var btnReset = document.getElementById("btnReset");
var semaineSelect;
btnReset.addEventListener("click", function () {
  var semaineValue = document.getElementById("semaine").value;
  semaineSelect = semaine.selectedIndex;
  semaine.selectedIndex = semaineSelect;
  resetError();
  fetchMenu(semaineValue);
  test();
  console.log(semaine.selectedIndex);
});

function test() {
  semaine.selectedIndex = 2;
  console.log(semaine.selectedIndex);
}
function resetError() {
  var errorMsgC = document.getElementsByClassName("errorMsg");
  for (var i = 0; i < errorMsgC.length; i++) {
    errorMsgC[i].classList.add("alert");
    errorMsgC[i].innerHTML = "";
  }
}
