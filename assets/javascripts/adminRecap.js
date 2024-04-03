import axios from "axios";
import "../styles/adminRecap.css";

axios
  .get("/recapSemaineJson")
  .then(function (response) {
    insertOptions(response.data);
  })
  .catch(function (error) {
    // en cas d’échec de la requête
    console.log(error);
  })
  .finally(function () {
    // dans tous les cas
  });

function insertOptions(data) {
  var semaineData = data["semaines"];
  var sectionData = data["sections"];
  insertSemaine(semaineData);
  insertSection(sectionData);
}

function insertSemaine(data) {
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

    semaine.appendChild(option);
  }
}
function insertSection(data) {
  var section = document.getElementById("section");
  section.innerHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];
    console.log(item);

    // Create an option element and set its text content
    const option = document.createElement("option");
    option.textContent = item.abreviation + " : " + item.nomSection;

    section.appendChild(option);
  }
  //   var optionPassed = semaine.options[0].textContent;
  //   var optionSplit = optionPassed.split(" ");
  //   var numeroOption = optionSplit[0];
  //   fetchMenu(numeroOption);
}
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
