import axios from "axios";
import "../styles/profil.css";

axios
  .get("/admin/gestion/user/promoJson")
  .then(function (response) {
    insertSectionOption(response.data);
  })
  .catch(function (error) {
    // en cas d’échec de la requête
    console.log(error);
  })
  .finally(function () {
    // dans tous les cas
  });

//
var promoSelect = document.getElementById("promoSelect");
function insertSectionOption(data) {
  promoSelect.innerHTML = "";
  var optionsHTML = "";
  for (let i = 0; i < data.length; i++) {
    const item = data[i];
    var promoId = item.id;
    var promoNom = item.nomPromo;
    var section = item.Section.nomSection;
    // Parse date strings to Date objects
    optionsHTML += `<option value="${promoId}">Section: ${section} Promo: ${promoNom}</option>`;
  }
  // Append the option to the select element
  promoSelect.innerHTML = optionsHTML;
  var optionPassed = promoSelect.options[0].value;
  fetchUserList(optionPassed);
}

promoSelect.addEventListener("change", function () {
  var selectedOption = this.options[this.selectedIndex];
  var selectedOptionValue = selectedOption.value;
  fetchUserList(selectedOptionValue);
});

function fetchUserList(value) {
  axios
    .get("/admin/gestion/user/listJson/" + value)
    .then(function (response) {
      insertUserOption(response.data);
    })
    .catch(function (error) {
      // en cas d’échec de la requête
      console.log(error);
    })
    .finally(function () {
      // dans tous les cas
    });
}
var user = document.getElementById("userSelect");
function insertUserOption(data) {
  user.innerHTML = "";
  var optionsHTML = "";

  for (let i = 0; i < data.length; i++) {
    const item = data[i];
    var userId = item.id;
    var userNom = item.userInfo.nom;
    var userPrenom = item.userInfo.prenom;
    // Parse date strings to Date objects
    optionsHTML += `<option value="${userId}"> ${userNom}  ${userPrenom}</option>`;
  }
  // Append the option to the select element
  user.innerHTML = optionsHTML;

  var optionPassed = user.options[0].value;
  fetchProfil(optionPassed);
}
user.addEventListener("change", function () {
  var selectedOption = this.options[this.selectedIndex];
  var selectedOptionValue = selectedOption.value;
  fetchProfil(selectedOptionValue);
});
function fetchProfil(value) {
  axios
    .get("/admin/gestion/userJson/" + value)
    .then(function (response) {
      insertProfil(response.data);
    })
    .catch(function (error) {
      // en cas d’échec de la requête
      console.log(error);
    })
    .finally(function () {
      // dans tous les cas
    });
}
function insertProfil(data) {
  var nom = data.userInfo.nom;
  var prenom = data.userInfo.prenom;
  var dateDeNaissance = new Date(data.userInfo.dateDeNaissance);
  var email = data.email;
  var identifiant = data.identifiant;
  var section = data.userInfo.promo.Section.nomSection;
  var promo = data.userInfo.promo.nomPromo;
  var dateDeDebut = new Date(data.userInfo.promo.dateDebut);
  var dateDeFin = new Date(data.userInfo.promo.dateFin);
  var montantGlobal = data.userInfo.montantGlobal;
  var roles = data.roles;

  document.getElementById("nom").textContent = nom;
  document.getElementById("prenom").textContent = prenom;
  document.getElementById("dateDeNaissance").textContent =
    formatDate(dateDeNaissance);
  document.getElementById("email").textContent = email;
  document.getElementById("identifiant").textContent = identifiant;
  document.getElementById("section").textContent = section;
  document.getElementById("promo").textContent = promo;
  document.getElementById("dateDeDebut").textContent = formatDate(dateDeDebut);
  document.getElementById("dateDeFin").textContent = formatDate(dateDeFin);
  document.getElementById("montantGlobal").textContent = montantGlobal;
  for (let i = 0; i < roles.length; i++) {
    if (roles[i] === "ROLE_DELEGUE") {
      document.getElementById("delegue").checked = true;
      break;
    } else {
      document.getElementById("delegue").checked = false;
    }
  }
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
document.getElementById("btnModifier").addEventListener("click", function () {
  // Get the selected value of the select element
  var selectedUserId = document.getElementById("userSelect").value;

  // Retrieve the path from the data attribute
  var path = "/admin/gestion/modif/" + selectedUserId;

  // Redirect to the constructed path
  window.location.href = path;
});

document.getElementById("btnDelete").addEventListener("click", function () {
  // Get the selected value of the select element
  var selectedReservationId = document.getElementById("semaine").value;

  // Retrieve the path from the data attribute
  var path = "/user/reservation/delete/" + selectedReservationId;

  // Redirect to the constructed path
  window.location.href = path;
});
