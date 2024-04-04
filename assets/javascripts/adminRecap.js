import axios from "axios";
import "../styles/adminRecap.css";

//Fetch Options pour select

axios
  .get("/admin/recap/SemaineJson")
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

//SELECT SEMAINE
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

////SELECT SECTION
function insertSection(data) {
  var section = document.getElementById("section");
  section.innerHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];

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

//CHANGE ON SELECT

var section = document.getElementById("section");
var semaine = document.getElementById("semaine");
section.addEventListener("change", handleChange);
semaine.addEventListener("change", handleChange);

function handleChange(event) {
  //semaine
  var semaineValue = document.getElementById("semaine").value;
  var semaineSplit = semaineValue.split(" ");
  var numeroOption = semaineSplit[0];
  //section
  var sectionValue = document.getElementById("section").value;
  var sectionSplit = sectionValue.split(" ");
  var abreviationOption = sectionSplit[0];
  //call function
  resetStyle();
  fetchRecap(abreviationOption, numeroOption);
}

//FETCH RECAP
function fetchRecap(sectionChoisi, SemaineChoisi) {
  axios
    .get("/admin/recapJson/" + sectionChoisi + "/" + SemaineChoisi)
    .then(function (response) {
      insertRecap(response.data);
    })
    .catch(function (error) {
      // en cas d’échec de la requête
      console.log(error);
    })
    .finally(function () {
      // dans tous les cas
    });
}

//SHOW RECAP
function insertRecap(data) {
  var recap = document.getElementById("recap");
  data.forEach((userData) => {
    var user = userData.user;
    var reservation = userData.reservation;

    var nom = user.userInfo.nom;
    var prenom = user.userInfo.prenom;
    var formattedNom = nom + " " + prenom;
    var newRowData = "";

    var newRow = document.createElement("tr");

    newRowData = "<tr>" + "<td class='nomRow'>" + formattedNom + "</td>";

    if (reservation != null) {
      var JourReservation = reservation.semaine.jourReservation;
      var repasReserveResa = reservation.repasReserves;
      JourReservation.forEach((jour) => {
        var repasJour = jour.repas;

        repasJour.forEach((repas) => {
          var reserveValid = false;
          var repasReserves = repas.repasReserves;
          var userReduc = userData.tarifReduc;

          if (userReduc === true) {
            var tarif = repas.typeRepas.tarifReduit;
          } else {
            var tarif = repas.typeRepas.tarifPlein;
          }
          repasReserves.forEach((repasReserve) => {
            repasReserveResa.forEach((reserve) => {
              if (repasReserve.id === reserve.id) {
                reserveValid = true;
              }
            });
          });
          if (reserveValid === true) {
            newRowData += "<td>" + tarif + "</td>";
          } else {
            newRowData += "<td></td>";
          }
        });
      });
      var montant = reservation.montantTotal;
      newRowData += "<td>" + montant + "</td></tr>";
    } else {
      for (let i = 0; i < 16; i++) {
        newRowData += " <td></td>";
      }
      newRowData += "<td>0</td></tr>";
    }

    newRow.innerHTML = newRowData;
    recap.appendChild(newRow);
  });
}
//RESET STYLE
function resetStyle() {
  var recap = document.getElementById("recap");
  var dynamicRows = recap.querySelectorAll("tr:not(:first-child)");
  dynamicRows.forEach(function (row) {
    recap.removeChild(row);
  });
}
//Print

var btnPrint = document.getElementById("btnPrint");
btnPrint.addEventListener("click", function () {
  window.print();
});
