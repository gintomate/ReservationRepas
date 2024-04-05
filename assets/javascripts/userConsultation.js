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
fetchReservation(1);
//function to change on select
semaine.addEventListener("change", function () {
  var selectedOption = this.options[this.selectedIndex];
  var selectedOptionText = selectedOption.textContent;
  var optionSplit = selectedOptionText.split(" ");
  var numeroOption = optionSplit[0];
  fetchReservation(numeroOption);
});

function fetchReservation(selectedOptionText) {
  axios
    .get("/user/consultationJson/" + selectedOptionText)
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
  const repasReserves = reservation.repasReserves;
  console.log(repasReserves);
  repasReserves.forEach((repasRes) => {
    var typeRepas = repasRes.repas.typeRepas.type;
    var jourRepas = repasRes.repas.jourReservation.dateJour ;
    console.log(typeRepas, jourRepas);
    const date = new Date(jour[i].dateJour);
    const options = { weekday: "long" };
    const jourIndex = date.toLocaleDateString("en-EN", options);
    switch (key) {
        case value:
            
            break;
    
        default:
            break;
    }
  });
}

function getDayClass(day) {
  var dayClass = document.getElementsByClassName(day);
  const dayArray = Array.from(dayClass);
  return dayArray;
}
