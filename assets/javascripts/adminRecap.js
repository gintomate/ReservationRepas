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
  var section = document.getElementById("section");
  section.innerHTML = "";
  var optionsHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];
    var sectionId = item.id;
    optionsHTML += `<option value="${sectionId}">${item.nomPromo} :  ${item.Section.nomSection}</option>`;
  }
  section.innerHTML = optionsHTML;
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
            newRowData += "<td class='responsiveHide'>" + tarif + "</td>";
          } else {
            newRowData += "<td class='responsiveHide'></td>";
          }
        });
      });
      var montant = reservation.montantTotal;
      newRowData += "<td>" + montant + "</td></tr>";
    } else {
      for (let i = 0; i < 16; i++) {
        newRowData += " <td class='responsiveHide'></td>";
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
