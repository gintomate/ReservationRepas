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
  var optionsHTML = "";
  for (let i = 0; i < Math.min(data.length, 20); i++) {
    const item = data[i];
    var promoId = item.id;
    // Parse date strings to Date objects
    optionsHTML += `<option value="${promoId}">${item.nomPromo}: ${formatDate(
      new Date(item.dateDebut)
    )} - ${formatDate(new Date(item.dateFin))}</option>`;
  }
  promo.innerHTML = optionsHTML;

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
  resetStyle();
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
  var recap = document.getElementById("recap");
  data.forEach((user) => {
    var montantGlobal = user.userInfo.montantGlobal;
    var nom = user.userInfo.nom;
    var prenom = user.userInfo.prenom;
    var email = user.email;
    var roles = user.roles;
    var userId = user.id;
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

document.getElementById("btnPromo").addEventListener("click", function () {
  // Get the selected value of the select element
  var selectedPromoId = document.getElementById("promo").value;

  // Retrieve the path from the data attribute
  var path = "/admin/promo/update/" + selectedPromoId;

  // Redirect to the constructed path
  window.location.href = path;
});
