import axios from "axios";
import "./styles/reservation.css";

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
semaine.addEventListener("change", function () {
  var selectedOption = this.options[this.selectedIndex];
  var selectedOptionValue = selectedOption.value; // If you have a value attribute
  var selectedOptionText = selectedOption.textContent;

  // Perform fetch or any other action here based on the selected option
  console.log("Selected Option: ", selectedOptionText);
  console.log("Selected Option Value: ", selectedOptionValue);
});

axios
  .get("/user/reservation/repasJson/1")
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

function insertRepas(data) {
  var jour = data.jourReservation;
  for (let i = 0; i < jour.length; i++) {
    jour[i].id;
    var date = new Date(jour[i].dateJour);
    var options = { weekday: "long" };
    var jourIndex = date.toLocaleDateString("fr-FR", options);

    if (jour.ferie === true) {
      console.log("ferie");
    } else {
      var repas = jour[i].repas;
      const dayArray = getDayClass(jourIndex);

      for (let j = 0; j < repas.length; j++) {
        var type = repas[j].typeRepas.type;
        switch (type) {
          case "petit_déjeuner":
            var description = repas[j].description;
            var formattedText = description.replace(/\n/g, "<br>");
            dayArray[0].innerHTML = formattedText;
            break;
          case "déjeuner_a":
            var description = repas[j].description;
            var formattedText = description.replace(/\n/g, "<br>");
            dayArray[1].innerHTML = formattedText;
          case "déjeuner_b":
            var description = repas[j].description;
            var formattedText = description.replace(/\n/g, "<br>");
            dayArray[2].innerHTML = formattedText;
            break;
          case "diner":
            var description = repas[j].description;
            var formattedText = description.replace(/\n/g, "<br>");
            dayArray[3].innerHTML = formattedText;
            break;
          default:
            break;
        }
      }
    }
  }
}

function getDayClass(day) {
  var dayClass = document.getElementsByClassName(day);
  const dayArray = Array.from(dayClass);
  return dayArray;
}
