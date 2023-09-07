/**
 * @author Mathis NIVEAU 
 * @date   21/03/2023
 * @description Ce fichier est utilisé exclusivement pour la page groupes.html
 * @version 1.1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier comporte plusieurs fonctions qui permettant de faire fonctionner l'intégralité de la page
 * - Fonction generateur_pin()
 * - Fonction afficher_groupes_API()
 * - Fonction ajouter_groupe_API()
 * - Fonction supprimer_groupe_API(id)
 * - Fonction supprimer_tout_groupe_API()
 */

// Variable image : avatar de chaque groupe en base64
var image_base64 = "";

// url de l'api pour faciliter le changement d'adresse IP vers API
var url_api = localStorage.getItem('url_api');

/**
 * @Brief Fonction permettant la génération de code PIN pour chaque groupe
 */
function generateur_pin() {

  const lettres = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

  // Génération des 3 lettres aléatoires
  let lettre1 = lettres.charAt(Math.floor(Math.random() * lettres.length));
  let lettre2 = lettres.charAt(Math.floor(Math.random() * lettres.length));
  let lettre3 = lettres.charAt(Math.floor(Math.random() * lettres.length));

  // Génération des 3 chiffres aléatoires
  let chiffre1 = Math.floor(Math.random() * 10);
  let chiffre2 = Math.floor(Math.random() * 10);
  let chiffre3 = Math.floor(Math.random() * 10);

  // Assemblage du PIN
  let pin_groupe =
    "#" + lettre1 + lettre2 + lettre3 + chiffre1 + chiffre2 + chiffre3;

  if (pin_groupe == null) {
    pin_groupe = "Erreur de génération";
  }
  // Affichage dans l'input associé
  document.getElementById("pin_groupe").value = pin_groupe;
}

/**
 * @brief Fonction permettant de transformer une image en BASE64 pour envoie json vers appli
 * @returns L'image chiffrer en base64
 */
function traitement_avatar_groupe() {

  // Récuperation du fichier image
  const image_input = document.getElementById("avatar_groupe");
  let image = image_input.files[0];

  // Importation FileReader pour convertion vers base64
  const reader = new FileReader();
  reader.readAsDataURL(image);
  reader.onload = function () {

    image_base64 = reader.result.split(',')[1];
    console.log("La conversion en base64 a été effectuée");
  }
  return image_base64;
}

/**
 * @brief Fonction permettant d'afficher l'ensemble des groupes créés
 */
function afficher_groupes_API() {

  fetch(url_api + "groupe/get_groupe.php")
    .then((response) => response.json())
    .then((data) => {

      // Connexion et récuperation des données réussi
      console.log("Connexion à l'API validée.");
      console.log("Récuperation des données validée.");

      // filtre permettant de récuperer les infos de la table groupe
      const groupes = data.groupe;
      const groupes_container = document.querySelector("#groupes-container");

      // Création des box dynamiquement en js -> html pour chaque groupe
      groupes.forEach((data) => {

        // Création de la box dans la page html
        const groupe_box = document.createElement("div");
        groupe_box.classList.add("groupe-box");

        // Ajout de l'image de groupe
        const image_groupe = document.createElement("img");
        image_groupe.src = data.avatar;
        image_groupe.classList.add("avatar-groupe");
        groupe_box.appendChild(image_groupe);

        // Ajout div pour infos du groupe
        const info_groupe = document.createElement("div");
        info_groupe.classList.add("info-groupe");

        // Ajout du nom de groupe
        const nom_groupe = document.createElement("p");
        nom_groupe.textContent = "Nom du groupe : " + data.nom;
        info_groupe.appendChild(nom_groupe);

        // Ajout du code pin de groupe
        const code_pin = document.createElement("p");
        code_pin.textContent = "Code pin : " + data.pin;
        info_groupe.appendChild(code_pin);

        groupe_box.appendChild(info_groupe);

        // Ajout de la case à cocher
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.id = "bin-groupe";
        groupe_box.appendChild(checkbox);

        // Suppression du groupe dans le html + appel fonction DELETE pour API
        checkbox.addEventListener("click", function () {
          supprimer_groupe_API(data.id);
          groupe_box.remove();
        });

        groupes_container.appendChild(groupe_box);
      });
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données :", error);
      console.log("Vérifier l'état de la connexion internet et l'adresse IP sur laquelle les données sont récupérées. ")
    });
}

/**
 * @brief Fonction permettant d'ajouter un groupe
 * @param form Recupere les données entrées dans le "form"
 */
function ajouter_groupe_API(form) {
  avatar_groupe = traitement_avatar_groupe();

  let data = {
    nom: form.nom_groupe.value,
    pin: form.pin_groupe.value,
    avatar: avatar_groupe,
  }
  fetch(url_api + "groupe/post_groupe.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data)
  })
    // Affichage des messages erreurs ou non
    .then((response) => {
      if (response.ok) {
        console.log("Les données ont été envoyées avec succès !");
        location.reload();
      } else {
        response.json().then(function (data) {
          console.log("Une erreur s'est produite lors de l'ajout du groupe.", data.message);
          // Afficher le message d'erreur à l'utilisateur
          alert(data.message);
        });
      }
    })
    .catch((error) => {
      console.error("Erreur : ", error);
    });
}

/**
 * @brief Fonction permettant de supprimer un groupe
 * @param id Permet de récuperer l'ID de la ligne a DELETE dans la db
 */
function supprimer_groupe_API(id) {
  // Formatage json pour envoie de l'id du groupe
  let data = {
    id: id
  }
  // Supprimer un groupe de l'API
  fetch(url_api + "groupe/delete_groupe.php", {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data)
  })
    // Affichage des messages erreurs ou non
    .then((response) => {
      if (response.ok) {
        console.log("Données supprimées avec succès !");
      } else {
        response.json().then(function (data) {
          console.log("Une erreur s'est produite lors de la suppression du groupe.", data.message);
        });
      }
    })
    .catch((error) => {
      console.error("Erreur : ", error);
    });
}

/**
 * @brief Fonction supprimant tous les élements de la table groupe
 */
function supprimer_tout_groupe_API() {
  // Supprimer tous les groupes de la page HTML
  let groupes_boxes = document.querySelectorAll(".groupe-box");
  groupes_boxes.forEach((groupe_box) => {
    groupe_box.remove();
  });
  // Supprimer tous les groupes de l'API
  fetch(url_api + "groupe/delete_all_groupe.php", {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ all: true })
  })
    // Affichage des messages erreurs ou non
    .then((response) => {
      if (response.ok) {
        console.log("Données supprimées avec succès !");
      } else {
        response.json().then(function (data) {
          console.log("Une erreur s'est produite lors de la suppression des groupes.", data.message);
        });
      }
    })
    .catch((error) => {
      console.error("Erreur : ", error);
    });
}
