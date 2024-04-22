import axios from "axios";
import "../styles/delegue.css";

axios
  .get("/delegue/SemaineJson")
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
    optionsHTML += `<option value="${semaineId}">${
      item.numeroSemaine
    }: ${formatDate(new Date(item.dateDebut))} - ${formatDate(
      new Date(item.dateFin)
    )}</option>`;
  }

  semaine.innerHTML = optionsHTML;
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

  resetStyle();
  fetchMenu(selectedOptionValue);
});
//FETCH RECAP
function fetchMenu(selectedOption) {
  axios
    .get("/delegue/recapJson/" + selectedOption)
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
    var montantTotal = userData.montantTotal; 
    var nom = user.userInfo.nom;
    var prenom = user.userInfo.prenom;
    var newRowData = "";

    var newRow = document.createElement("tr");

    newRowData =
      "<tr>" +
      "<td>" +
      nom +
      "</td><td>" +
      prenom +
      "</td><td>" +
      montantTotal +
      "</td><td></td><td></td><td></td>";

    newRow.innerHTML = newRowData;
    recap.appendChild(newRow);
  });
}
//RESET STYLE
function resetStyle() {
  var recap = document.getElementById("recap");
  var dynamicRows = recap.querySelectorAll("tr");
  dynamicRows.forEach(function (row) {
    recap.removeChild(row);
  });
}

//Print

var btnPrint = document.getElementById("btnPrint");
btnPrint.addEventListener("click", function () {
  window.print();
});
