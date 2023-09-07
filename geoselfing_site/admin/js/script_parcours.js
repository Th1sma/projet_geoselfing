/**
 * @author Mathis NIVEAU
 * @date   11/05/2023
 * @description Ce fichier est utilisé exclusivement pour la page parcours.html
 * @version 1.0.0
 * @implements API MAPBOX pour affichage et recuperation des positions markers pour ajouter des points
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier comporte plusieurs fonctions qui permettant de faire fonctionner l'intégralité de la page
 * - Fonction afficher_map()
 * - Fonction afficher_parcours_API()
 * - Fonction afficher_groupes_API()
 * - Fonction envoyer_parcours_API(formulaire)
 * - Fonction supprimer_parcours_API(id)
 * - Fonction supprimer_tout_parcours_API()
 */

const accessToken =
  "pk.eyJ1IjoiZ2Vvc2VsZmluZyIsImEiOiJjbGRvZGNjOWowMGQ2M3dxd3F1ODI1M2h4In0.j5vyEzDBIq-_i-_LVWeEYQ";

// Tableau pour stocker les coordonnées et les données des points de parcours
var parcours_data = [];

// Variable de stockage map
var map;

// Variable Stockage markers map (tracage parcours)
var markers = [];

// url de l'api pour faciliter le changement d'adresse IP vers API
var url_api = localStorage.getItem('url_api');

/**
 * @brief Fonction permettant l'affichage de la map sur la page + points déjà placés
 * @returns La map
 */
function afficher_map_creation() {
  // Initialisation de la carte
  mapboxgl.accessToken = accessToken;
  map = new mapboxgl.Map({
    container: "map",
    style: "mapbox://styles/mapbox/outdoors-v11",
    center: [-0.45877, 46.32313],
    zoom: 14,
  });

  // Événement de chargement du style de la carte
  map.on("load", function () {

    // Ajouter une source de données pour l'itinéraire
    map.addSource("route", {
      type: "geojson",
      data: {
        type: "Feature",
        geometry: {
          type: "LineString",
          coordinates: [],
        },
      },
    });

    // Ajouter une couche de style pour l'itinéraire
    map.addLayer({
      id: "route",
      type: "line",
      source: "route",
      layout: {
        "line-join": "round",
        "line-cap": "round",
      },
      paint: {
        "line-color": "#333",
        "line-width": 3,
        "line-dasharray": [3, 2],
      },
    });

    // Événement de clic sur la carte
    map.on("click", function (event) {
      // Récuperation des coordonnées
      var coordinates = event.lngLat;
      var longitude = coordinates.lng;
      var latitude = coordinates.lat;

      // Créer une fenêtre contextuelle (popup) avec un formulaire d'entrée de données
      var popup = new mapboxgl.Popup()
        .setLngLat([longitude, latitude])
        .setHTML(
          `
          <h3>Ajouter un point de parcours</h3>
          <form id="parcours-form">
            <label for="nom">Nom du point:</label>
            <input type="text" id="nom" name="nom" required>

            <label for="note">Note du point:</label>
            <textarea id="note" name="note" required></textarea>

            <button type="submit">Ajouter</button>
          </form>
        `
        )
        .addTo(map);

      // Événement de soumission du formulaire
      document
        .getElementById("parcours-form")
        .addEventListener("submit", function (event) {
          event.preventDefault();

          // Récupérer les valeurs du formulaire
          var nom = document.getElementById("nom").value;
          var note = document.getElementById("note").value;

          // Créer un marqueur pour le point de parcours
          var marker = new mapboxgl.Marker()
            .setLngLat([longitude, latitude])
            .addTo(map);

          // Ajouter les coordonnées et les données du point de parcours au tableau
          parcours_data.push({
            coordinates: [longitude, latitude],
            nom: nom,
            note: note,
          });

          // Mettre à jour la source de données de l'itinéraire
          map.getSource("route").setData({
            type: "Feature",
            properties: {},
            geometry: {
              type: "LineString",
              coordinates: parcours_data.map((point) => point.coordinates),
            },
          });

          // Fermer la fenêtre contextuelle (popup)
          popup.remove();
        });
    });
  });

  return map;
}

/**
 * @brief Fonction permettant d'afficher l'ensemble des parcours déjà enregistrés dans la db API
 * @note Cette fonction permet l'affichage des parcours ainsi que des groupes associés a chaque parcours à l'aide de fetch et d'asynchrone 
 */
function afficher_parcours_API() {
  // Appel à l'API pour récupérer les parcours
  fetch(url_api + "parcours/get_parcours.php")
    .then((response) => response.json())
    .then((data) => {
      // Connexion et récupération des données réussies
      console.log("Connexion à l'API validée.");
      console.log("Récupération des données validée.");

      // Récupération du conteneur des parcours dans le HTML
      const parcours_container = document.querySelector(".parcours-container");

      // Parcours des parcours
      const parcours_promises = data.parcours.map((parcours) => {
        // Requête pour récupérer le groupe associé au parcours
        return fetch(url_api + `groupe/get_groupe.php?groupe_fk_parcours=${parcours.id_parcours}`)
          .then((response) => response.json())
          .then((data) => {
            return {
              parcours: parcours,
              groupe: data.groupe
            };
          })
          .catch((error) => {
            console.error("Erreur lors de la récupération des données du groupe :", error);
            return {
              parcours: parcours,
              groupe: null
            };
          });
      });
      // Traitement lorsque toutes les requetes précédente ont réussi. 
      Promise.all(parcours_promises)
        .then((results) => {
          // Traiter les résultats de toutes les requêtes
          results.forEach((result) => {
            const parcours = result.parcours;
            const groupe = result.groupe;

            // Ignorer le parcours s'il n'y a pas de groupe associé
            if (groupe === null) {
              return;
            }

            // Création d'un tableau pour chaque parcours
            const table = document.createElement("table");
            table.className = "parcours-table";

            // Ajout du titre du parcours avec une case à cocher
            const caption = document.createElement("caption");
            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.id = "checkbox-parcours-" + parcours.id_parcours; // un identifiant unique pour chaque parcours
            const label = document.createElement("label");
            label.textContent = parcours.nom_parcours + " | " + groupe[0].nom;

            caption.appendChild(checkbox);
            caption.appendChild(label);
            table.appendChild(caption);

            // Suppression du parcours dans le html + appel fonction DELETE pour API
            checkbox.addEventListener("click", function () {
              supprimer_parcours_API(parcours.id_parcours);
              table.remove();
            });

            // Événement de clic sur le tableau de parcours
            table.addEventListener("click", function () {
              // Appeler la fonction pour afficher le tracé du parcours sur la carte
              afficher_tracer_parcours(parcours.nom_parcours, map);
            });

            // Création de l'en-tête du tableau
            const thead = document.createElement("thead");
            const tr = document.createElement("tr");
            const th_id = document.createElement("th");
            th_id.textContent = "Ordre";
            const th_nom = document.createElement("th");
            th_nom.textContent = "Nom";
            const th_note = document.createElement("th");
            th_note.textContent = "Note";

            tr.appendChild(th_id);
            tr.appendChild(th_nom);
            tr.appendChild(th_note);
            thead.appendChild(tr);
            table.appendChild(thead);

            // Création du corps du tableau
            const tbody = document.createElement("tbody");
            let ordre_points = 1;
            parcours.points.forEach((point) => {
              const tr = document.createElement("tr");
              const td_id = document.createElement("td");
              td_id.textContent = ordre_points++;
              const td_nom = document.createElement("td");
              td_nom.textContent = point.nom;
              const td_note = document.createElement("td");
              td_note.textContent = point.note;

              tr.appendChild(td_id);
              tr.appendChild(td_nom);
              tr.appendChild(td_note);
              tbody.appendChild(tr);
            });

            table.appendChild(tbody);

            // Ajout du tableau au conteneur des parcours
            parcours_container.appendChild(table);
          });
        })
        .catch((error) => {
          console.error("Erreur lors de la récupération des données des groupes :", error);
        });
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données :", error);
      console.log("Vérifier l'état de la connexion internet et l'adresse IP sur laquelle les données sont récupérées.");
    });
}

/**
 * @brief Fonction permettant d'afficher le tracer de chaque parcours en cliquant sur le tableau du parcours (voir fonction si dessus pour l'event)
 * @param nom_parcours Permet de recuperer le parcours à afficher sur la map
 * @param map Permet de set les markers et les LineString sur la map pour le tracer
 */
function afficher_tracer_parcours(nom_parcours, map) {
  // Supprimer les anciens marqueurs du tableau
  markers.forEach((marker) => marker.remove());
  markers = [];

  // Requête GET pour obtenir les coordonnées du parcours à partir de l'API
  fetch(url_api + `parcours/get_parcours.php?nom_parcours=${nom_parcours}`)
    .then((response) => response.json())
    .then((data) => {
      // Vérifier si le parcours a été trouvé
      if (data.parcours.length > 0) {
        const parcours = data.parcours[0];

        // Récupérer les coordonnées du parcours
        const coordonnees = parcours.points;
        //console.log(coordonnees);
        // Créer un tableau de coordonnées pour le tracé
        const coordonnees_traces = coordonnees.map((coordonnee) => [
          coordonnee.longitude,
          coordonnee.latitude,
        ]);

        // Mettre à jour la source de données de l'itinéraire sur la carte
        map.getSource("route").setData({
          type: "Feature",
          properties: {},
          geometry: {
            type: "LineString",
            coordinates: coordonnees_traces,
          },
        });

        // Centrer la carte sur le tracé
        map.fitBounds(coordonnees_traces, {
          padding: 50,
          maxZoom: 15,
        });

        coordonnees.forEach((coordonnee, index) => {
          let marker_color = "red"; // Couleur par défaut pour les autres marqueurs
          if (index === 0) {
            marker_color = "green"; // Couleur verte pour le premier marqueur
          }

          const marker = new mapboxgl.Marker({ color: marker_color })
            .setLngLat([coordonnee.longitude, coordonnee.latitude])
            .addTo(map);

          markers.push(marker);
        });
      }
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des coordonnées du parcours :", error);
      console.log("Vérifier l'état de la connexion internet et l'adresse IP sur laquelle les données sont récupérées.");
    });
}

/**
 * @brief Fonction permettant de lister les groupes pour associé un groupe a un parcours
 */
function afficher_groupes_API() {
  // Appel à l'API pour récupérer les groupes
  fetch(url_api + "groupe/get_groupe.php")
    .then((response) => response.json())
    .then((data) => {
      // Connexion et récupération des données réussies
      console.log("Connexion à l'API validée. (groupe)");
      console.log("Récupération des données validée. (groupe)");

      const groupes = data.groupe;
      // Récupération du menu déroulant des groupes dans le HTML
      const groupe_select = document.getElementById("groupe");

      // Parcours des groupes
      groupes.forEach((groupe) => {
        if (groupe.groupe_fk_parcours < 1) {
          // Création d'une option pour chaque groupe
          const option = document.createElement("option");
          option.value = groupe.id;
          option.textContent = groupe.nom;

          // Ajout de l'option au menu déroulant des groupes
          groupe_select.appendChild(option);
        }
      });
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données :", error);
      console.log("Vérifier l'état de la connexion internet et l'adresse IP sur laquelle les données sont récupérées.");
    });
}

/**
 * @brief Fonction permettant d'envoyer un nouveau parcours à l'API et de lui associé un groupe
 * @param form Permet de recuperer les données entrées dans le form
 */
function envoyer_parcours_API(form) {
  // Verification que des groupes apparaissent dans le <select>
  if (form.groupe.value === "") {
    alert("Vous devez d'abord créer des groupes !");
  }
  // Construire l'objet JSON avec les données du parcours
  var parcours_json = {
    nom_parcours: form.nom_parcours.value,
    points: parcours_data,
    nom_groupe: form.groupe.options[form.groupe.selectedIndex].text,
  };
  //console.log(parcours_json);
  // Envoyer les données vers l'API
  fetch(url_api + "parcours/post_parcours.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(parcours_json),
  })
    // Affichage des messages erreurs ou non (reset du tableau de parcours)
    .then(function (response) {
      if (response.ok) {
        console.log("Les données ont été envoyées avec succès !");

        // Réinitialiser le tableau et la source de données de l'itinéraire
        parcours_data = [];
        map.getSource("route").setData({
          type: "Feature",
          properties: {},
          geometry: {
            type: "LineString",
            coordinates: [],
          },
        });
        location.reload();
      } else {
        response.json().then(function (data) {
          console.log("Une erreur s'est produite lors de l'envoi du parcours.",data.message);
          alert(data.message);
        });
      }
    })
    .catch((error) => {
      console.error("Erreur : ", error);
    });
}

/**
 * @brief Fonction permettant de supprimer seulement un parcours
 * @param id Permet de récupéré l'id du parcours à DELETE sur la table db
 */
function supprimer_parcours_API(id) {
  // Formatage JSON pour l'envoi de l'ID du parcours à supprimer
  let data = {
    id: id,
  };
  // Supprimer un parcours de l'API
  fetch(url_api + "parcours/delete_parcours.php", {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    // Affichage des messages erreurs ou non
    .then((response) => {
      if (response.ok) {
        console.log("Parcours supprimé avec succès !");
      } else {
        response.json().then(function (data) {
          console.log("Une erreur s'est produite lors de la suppression du parcours.",data.message);
        });
      }
      location.reload();
    })
    .catch((error) => {
      console.error("Erreur : ", error);
    });
}

/**
 * @brief Fonction supprimant tous les élements de la table parcours / coordonnées
 */
function supprimer_tout_parcours_API() {
  // Supprimer tous les parcours de la page HTML
  let parcours_boxes = document.querySelectorAll(".parcours-table");
  parcours_boxes.forEach((parcours_box) => {
    parcours_box.remove();
  });
  // Supprimer tous les parcours de l'API
  fetch(url_api + "parcours/delete_all_parcours.php", {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ all: true }),
  }
  )
    // Affichage des messages erreurs ou non
    .then((response) => {
      if (response.ok) {
        console.log("Données supprimées avec succès !");
      } else {
        response.json().then(function (data) {
          console.log("Une erreur s'est produite lors de la suppression des parcours.", data.message);
        });
      }
      location.reload();
    })
    .catch((error) => {
      console.error("Erreur : ", error);
    });
}
