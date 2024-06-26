import axios from "axios";
import "../styles/userConsultation.css";

axios
  .get("/admin/consultationSemaineJson")
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
//Select
function insertOption(data) {
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

semaine.addEventListener("change", function () {
  var selectedOption = this.options[this.selectedIndex];
  var selectedOptionValue = selectedOption.value;
  fetchMenu(selectedOptionValue);
});

function fetchMenu(value) {
  axios
    .get("/admin/consultationJson/+" + value)
    .then(function (response) {
      insertMenu(response.data);
    })
    .catch(function (error) {
      // en cas d’échec de la requête
      console.log(error);
    })
    .finally(function () {
      // dans tous les cas
    });
}
//Insert Menu
function insertMenu(menu) {
  const jourReservation = menu.jourReservation;
  const repasByDay = {
    Monday: [],
    Tuesday: [],
    Wednesday: [],
    Thursday: [],
    Friday: [],
  };

  jourReservation.forEach((jour) => {
    var ferie = jour.ferie;
    var repas = jour.repas;
    if (ferie) {
      console.log();
    } else {
      repas.forEach((repas) => {
        var typeRepas = repas.typeRepas.type;
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
        var jourRepas = jour.dateJour;

        const date = new Date(jourRepas);
        const options = { weekday: "long" };
        const jourIndex = date.toLocaleDateString("en-EN", options);
        repasByDay[jourIndex].push({ type: typeFormated, repas: repas });
      });
    }
  });

  for (const day in repasByDay) {
    if (Object.hasOwnProperty.call(repasByDay, day)) {
      const elements = repasByDay[day];
      var dayRepasContainer = document.getElementById(day);
      var recap = "";
      dayRepasContainer.innerHTML = "";
      if (elements.length < 1) {
        recap = "<div class='emptyJour'>Férié</div>";
      } else {
        elements.forEach((element) => {
          var description = element.repas.description;
          const formattedText = description.replace(/\n/g, "<br>");
          var type = element.type;
          recap +=
            "<div class='repas'><h4>" +
            type +
            "</h4><p>" +
            formattedText +
            "</p></div>";
        });
      }

      dayRepasContainer.innerHTML = recap;
    }
  }
}

//Modifier

document.getElementById("btnModifier").addEventListener("click", function () {
  // Get the selected value of the select element
  var selectedReservationId = document.getElementById("semaine").value;

  // Retrieve the path from the data attribute
  var path = "/admin/menu/modif/" + selectedReservationId;

  // Redirect to the constructed path
  window.location.href = path;
});

//Supprimer

document.getElementById("btnDelete").addEventListener("click", function () {
  // Get the selected value of the select element
  var selectedReservationId = document.getElementById("semaine").value;

  // Retrieve the path from the data attribute
  var path = "/admin/menu/delete/" + selectedReservationId;

  // Redirect to the constructed path
  window.location.href = path;
});
