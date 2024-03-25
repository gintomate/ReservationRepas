import axios from "axios";
import './styles/menu.css';
// Requêter un utilisateur avec un ID donné.
axios
  .get("/menu/creer/get")
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
  data.forEach((item) => {
    const li = document.createElement("option");
    li.textContent = item.numeroSemaine;
    semaine.appendChild(li);
  });
}
