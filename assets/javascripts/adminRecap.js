import axios from "axios";
import "../styles/adminRecap.css";
import { formatDate } from "./global.js";

//INITIALISATION
const btnPrint = document.getElementById("btnPrint");
const section = document.getElementById("section");
const semaine = document.getElementById("semaine");
const recap = document.getElementById("recap");

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

//FUNTION TO INSERT BOTH SELECT

function insertOptions(data) {
  var semaineData = data["semaines"];
  var sectionData = data["sections"];
  insertSemaine(semaineData);
  insertSection(sectionData);
  var semainePassed = semaine.options[0].value;
  var sectionPassed = section.options[0].value;
  fetchRecap(sectionPassed, semainePassed);
}

//SELECT SEMAINE

function insertSemaine(data) {
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
  }
  // Append the option to the select element
  semaine.innerHTML = optionsHTML;
}

////SELECT SECTION

function insertSection(data) {
  section.innerHTML = "";
  var optionsHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];
    var sectionId = item.id;
    optionsHTML += `<option value="${sectionId}">${item.nomPromo} :  ${item.Section.nomSection}</option>`;
  }
  section.innerHTML = optionsHTML;
}

//CHANGE ON SELECT

function handleChange() {
  //semaine
  var semaineValue = document.getElementById("semaine").value;
  //section
  var sectionValue = document.getElementById("section").value;
  //call function
  resetStyle();
  fetchRecap(sectionValue, semaineValue);
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
  data.forEach((userData) => {
    var user = userData.user;
    var reservation = userData.reservation;

    var nom = user.userInfo.nom;
    var prenom = user.userInfo.prenom;
    var formattedNom = nom + " " + prenom;
    var newRowData = "";

    var newRow = document.createElement("tr");

    newRowData = "<tr>" + "<td class='nomColumn'>" + formattedNom + "</td>";

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
            newRowData += "<td class='responsiveHide  recapData'>" + tarif + "</td>";
          } else {
            newRowData += "<td class='responsiveHide recapData'></td>";
          }
        });
      });
      var montant = reservation.montantTotal;
      newRowData += "<td>" + montant + "</td></tr>";
    } else {
      for (let i = 0; i < 16; i++) {
        newRowData += " <td class='responsiveHide  recapData'></td>";
      }
      newRowData += "<td>0</td></tr>";
    }

    newRow.innerHTML = newRowData;
    recap.appendChild(newRow);
  });
}
//RESET STYLE
function resetStyle() {
  var dynamicRows = recap.querySelectorAll("tr:not(:first-child)");
  dynamicRows.forEach(function (row) {
    recap.removeChild(row);
  });
}

section.addEventListener("change", handleChange);
semaine.addEventListener("change", handleChange);
//Print
btnPrint.addEventListener("click", function () {
  window.print();
});
