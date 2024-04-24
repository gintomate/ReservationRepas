import axios from "axios";
import "../styles/cuisine.css";

axios
  .get("/cuisine/SemaineJson")
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
  fetchRecap(optionPassed);
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
  fetchRecap(selectedOptionValue);
});
//FETCH RECAP
function fetchRecap(selectedOption) {
  axios
    .get("/cuisine/recap/Json/" + selectedOption)
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
  recap.innerHTML = "";

  data["jourReservation"].forEach((jour) => {
    var jourRepas = jour.dateJour;
    const date = new Date(jourRepas);
    const options = { weekday: "long" };
    const jourIndex = date.toLocaleDateString("fr", options);
    const modStr = jourIndex[0].toUpperCase() + jourIndex.slice(1);
    console.log(jourIndex, modStr);

    var ferie = jour.ferie;
    if (ferie) {
      var newRow = "<tr><td>" + modStr + "<td colspan='3'>Pas de Repas</td>";
      recap.innerHTML += newRow;
    } else {
      var repas = jour.repas;
      var newRow = "<tr><td rowspan='" + repas.length + "'>" + modStr + "</td>";

      repas.forEach((repas) => {
        var description = repas.description;
        var type = repas.typeRepas.type;
        var repasReserve = repas.repasReserves;
        var typeFormated = "";
        switch (type) {
          case "petit_déjeuner":
            typeFormated = "Petit Déjeuner";
            break;
          case "déjeuner_a":
            typeFormated = "Déjeuner";
            break;
          case "déjeuner_b":
            typeFormated = "Déjeuner B";
            break;
          case "diner":
            typeFormated = "Diner";
            break;
          default:
            typeFormated = "Erreur de Type.";
            break;
        }
        newRow +=
          "<td>" +
          typeFormated +
          "</td><td>" +
          description +
          "</td><td>" +
          repasReserve.length +
          "</td><tr>";
      });
      newRow += "</tr> ";

      recap.innerHTML += newRow;
    }
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
