import axios from "axios";
import "../styles/adminRecap.css";

const userJs = document.querySelector(".js-user");
const section = userJs.getAttribute("data-section");
axios
  .get("/admin/section/promoJson/" + section)
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
  var promo = document.getElementById("promo");
  promo.innerHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];

    var promoId = item.id;
    // Parse date strings to Date objects
    var dateDebut = new Date(item.dateDebut);
    var dateFin = new Date(item.dateFin);

    // Format date components to d-m-Y format
    var formattedDateDebut = formatDate(dateDebut);
    var formattedDateFin = formatDate(dateFin);

    // Create an option element and set its text content
    const option = document.createElement("option");
    option.textContent =
      item.nomPromo + ": " + formattedDateDebut + " - " + formattedDateFin;
    option.value = promoId;
    // Append the option to the select element
    promo.appendChild(option);
  }
  var optionPassed = promo.options[0].value;
  fetchUser(optionPassed);
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
promo.addEventListener("change", function () {
  var selectedOption = this.options[this.selectedIndex];
  var selectedOptionValue = selectedOption.value;
  fetchUser(selectedOptionValue);
});
//FETCH RECAP
function fetchUser(selectedOptionValue) {
  axios
    .get("/admin/sectionJson/" + selectedOptionValue)
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
  console.log(data);
  var recap = document.getElementById("recap");
  data.forEach((user) => {
    console.log(user);
    var montantGlobal = user.userInfo.montantGlobal;
    var nom = user.userInfo.nom;
    var prenom = user.userInfo.prenom;
    var email = user.email;
    var roles = user.roles;
    var userId = user.id;
    console.log(roles);
    let statut = "STATUS INDEFINI";
    let delegueFound = false;
    for (let i = 0; i < roles.length; i++) {
      if (roles[i] === "ROLE_DELEGUE") {
        statut = "Délégué";
        delegueFound = true;
        break;
      }
    }

    if (!delegueFound) {
      for (let i = 0; i < roles.length; i++) {
        const role = roles[i];
        console.log(role);
        switch (role) {
          case "ROLE_STAGIAIRE":
            statut = "Stagiaire";
            break;
          case "ROLE_PERSONNEL":
            statut = "Personnel";
            break;
          case "ROLE_CUISINIER":
            statut = "Cuisnier";
            break;
          default:
            statut = "STATUS INDEFINI";
            break;
        }

        if (statut !== "STATUS INDEFINI") {
          break;
        }
      }
    }

    var newRowData = "";

    var newRow = document.createElement("tr");

    newRowData =
      "<td>" +
      nom +
      "</td><td>" +
      prenom +
      "</td><td>" +
      email +
      "</td><td>" +
      statut +
      "</td><td>" +
      montantGlobal +
      "</td><td><a href='/admin/gestion/user'>" +
      userId +
      "</a></td>";

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
