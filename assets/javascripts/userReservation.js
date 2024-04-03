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
      item.numeroSemaine +
      " : " +
      formattedDateDebut +
      " - " +
      formattedDateFin;

    // Append the option to the select element
    semaine.appendChild(option);
  }
  var optionPassed = semaine.options[0].textContent;
  var optionSplit = optionPassed.split(" ");
  var numeroOption = optionSplit[0];
  fetchMenu(numeroOption);
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
  var selectedOptionText = selectedOption.textContent;
  var optionSplit = selectedOptionText.split(" ");
  var numeroOption = optionSplit[0];
  resetStyles();
  resetError();
  fetchMenu(numeroOption);
});

// function to fetch the menu

function fetchMenu(selectedOptionText) {
  axios
    .get("/user/reservation/repasJson/" + selectedOptionText)
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

var btnValider = document.getElementById("btnValider");
btnValider.addEventListener("click", formControl);

function formControl() {
  validateForm();
  callValid(validateForm());
}
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

function callValid(formValid) {
  var errorMsg = document.getElementById("errorMsg");
  if (!formValid) {
    errorMsg.innerHTML = "Veuillez cocher au moins une case.";
    errorMsg.classList.add("alert");
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
