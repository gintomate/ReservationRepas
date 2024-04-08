import axios from "axios";
import "../styles/UserConsultation.css";

axios
  .get("/user/consultationSemaineJson")
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
  var optionPassed = semaine.options[0].value;
  fetchReservation(optionPassed);
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
  fetchReservation(selectedOptionValue);
});

function fetchReservation(value) {
  axios
    .get("/user/consultationJson/" + value)
    .then(function (response) {
      insertReservation(response.data);
    })
    .catch(function (error) {
      // en cas d’échec de la requête
      console.log(error);
    })
    .finally(function () {
      // dans tous les cas
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
      dayRepasContainer.innerHTML = recap;
    }
  }
}

// function to show the total
function insertMontant(reservation) {
  const montant = document.getElementById("montant");
  const montantTotal = reservation.montantTotal;
  montant.textContent = montantTotal;
}

document.getElementById("btnValider").addEventListener("click", function () {
  // Get the selected value of the select element
  var selectedReservationId = document.getElementById("semaine").value;

  // Retrieve the path from the data attribute
  var path = "/user/reservation/modif/" + selectedReservationId;

  // Redirect to the constructed path
  window.location.href = path;
});
