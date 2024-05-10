import axios from "axios";
import "../styles/UserConsultation.css";
import { formatDate } from "./global.js";

function fetchSemaine() {
  axios
    .get("/user/consultationSemaineJson")
    .then(function (response) {
      insertOption(response.data);
    })
    .catch(function (error) {
      // en cas d’échec de la requête
      console.log(error);
    });
}

function insertOption(data) {
  var semaine = document.getElementById("semaine");
  semaine.innerHTML = "";
  var optionsHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];
    var reservId = item.id;
    // Parse date strings to Date objects
    optionsHTML += `<option value="${reservId}">${
      item.semaine.numeroSemaine
    }: ${formatDate(new Date(item.semaine.dateDebut))} - ${formatDate(
      new Date(item.semaine.dateFin)
    )}</option>`;
  }
  semaine.innerHTML = optionsHTML;
  var optionPassed = semaine.options[0].value;
  fetchReservation(optionPassed);
}

function fetchReservation(value) {
  axios
    .get("/user/consultationJson/" + value)
    .then(function (response) {
      insertReservation(response.data);
    })
    .catch(function (error) {
      // en cas d’échec de la requête
      console.log(error);
    });
}

function insertReservation(reservation) {
  insertMontant(reservation);
  const repasReserves = reservation.repasReserves;
  const repasByDay = {
    Monday: [],
    Tuesday: [],
    Wednesday: [],
    Thursday: [],
    Friday: [],
  };

  repasReserves.forEach((repasRes) => {
    var typeRepas = repasRes.repas.typeRepas.type;
    var typeFormated = "";
    switch (typeRepas) {
      case "petit_déjeuner":
        typeFormated = "Petit Déjeuner";
        break;
      case "déjeuner_a":
        typeFormated = "Déjeuner A";
        break;
      case "déjeuner_b":
        typeFormated = "Déjeuner B";
        break;
      case "diner":
        typeFormated = "Diner ";
        break;
    }
    var jourRepas = repasRes.repas.jourReservation.dateJour;
    const date = new Date(jourRepas);
    const options = { weekday: "long" };
    const jourIndex = date.toLocaleDateString("en-EN", options);

    repasByDay[jourIndex].push({ type: typeFormated, repasRes: repasRes });
  });

  for (const day in repasByDay) {
    if (Object.hasOwnProperty.call(repasByDay, day)) {
      const elements = repasByDay[day];
      var dayRepasContainer = document.getElementById(day);
      var recap = "";
      dayRepasContainer.innerHTML = "";
      if (elements.length < 1) {
        // If there are no elements, set the content to indicate that it's empty
        recap = "<div class='emptyJour'>Pas de Repas</div>";
      } else {
        // If there are elements, iterate through each element and build the HTML content
        elements.forEach((element) => {
          var description = element.repasRes.repas.description;
          const formattedText = description.replace(/\n/g, "<br>");

          var type = element.type;
          recap +=
            "<div class='repas'><h4>" +
            type +
            "</h4><p>" +
            formattedText +
            "</p></div>";
        });
        // Set the HTML content of dayRepasContainer to the built recap
        dayRepasContainer.innerHTML = recap;
      }
    }
  }
}

// function to show the total
function insertMontant(reservation) {
  const montant = document.getElementById("montant");
  const montantTotal = reservation.montantTotal;
  montant.value = montantTotal;
}

document.getElementById("btnValider").addEventListener("click", function () {
  // Get the selected value of the select element
  var selectedReservationId = document.getElementById("semaine").value;

  // Retrieve the path from the data attribute
  var path = "/user/reservation/modif/" + selectedReservationId;

  // Redirect to the constructed path
  window.location.href = path;
});

document.getElementById("btnDelete").addEventListener("click", function () {
  // Get the selected value of the select element
  var selectedReservationId = document.getElementById("semaine").value;
  // Retrieve the path from the data attribute
  var path = "/user/reservation/delete/" + selectedReservationId;
  // Redirect to the constructed path
  window.location.href = path;
});

document.addEventListener("DOMContentLoaded", () => {
  fetchSemaine();
  //function to change on select
  semaine.addEventListener("change", function () {
    var selectedOption = this.options[this.selectedIndex];
    var selectedOptionValue = selectedOption.value;
    fetchReservation(selectedOptionValue);
  });
});
