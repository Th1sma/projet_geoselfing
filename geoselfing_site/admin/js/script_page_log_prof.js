// url de l'api pour faciliter le changement d'adresse IP vers API
var url_api = localStorage.getItem('url_api');

let url_professeur = url_api + "professeur/log_prof.php";

// Login page professeur
function redirec() {
  var username_input = document.getElementById("username");
  var password_input = document.getElementById("password");
  var username_value = username_input.value;
  var password_value = password_input.value;

  const identification = {
    method: "POST",
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json;charset=UTF-8",
    },
    body: JSON.stringify({
      identifiant: username_value,
      mot_de_passe: password_value,
    }),
  };
  fetch(url_professeur, identification)
    .then((response) => {
      console.log(response);
      if (response.status === 201) {
        window.location.replace("../pages/dashboard.html");
      } else {
        alert("Identifiant ou mot de passe incorrect !");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}
