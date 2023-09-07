/**
 * @author Mathis NIVEAU
 * @date   21/03/2023
 * @description Ce fichier est utilisé exclusivement pour la page dashboard.html
 * @version 1.0.0
 * @implements API MAPBOX pour affichage et recuperation des positions markers
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier comporte plusieurs fonctions qui permettant de faire fonctionner certains aspects de la page
 * - Fonction afficher_map_container()
 * - Fonction afficher_derniers_groupes_API()
 * - Fonction afficher_recap_questions_API()
 * - Fonction supprimer_votes_score_API()
 * - Fonction generer_identifiants()
 */

// Variable récuperation nombre de point sur la map ( affichage sur le dashboard )
var compteur_parcours = 0;

// Variable récuperation nombre de groupe ( affichage sur le dashboard )
var compteur_groupe = 0;

// Variable récuperation nombre de question ( affichage sur le dashboard )
var compteur_questions = 0;

// Tableau contenant les identifiants
var identifiants = [];

// url de l'api pour faciliter le changement d'adresse IP vers API
var url_api = localStorage.getItem('url_api');

/**
 * @brief Fonction permettant l'affichage de la map sur la page + points déjà placés
 */
function afficher_map_container() {
  // Initialisation de la map MapBox
  mapboxgl.accessToken = "pk.eyJ1IjoiZ2Vvc2VsZmluZyIsImEiOiJjbGRvZGNjOWowMGQ2M3dxd3F1ODI1M2h4In0.j5vyEzDBIq-_i-_LVWeEYQ";
  const map = new mapboxgl.Map({
    container: "map-dashboard",
    style: "mapbox://styles/mapbox/streets-v11",
    center: [-0.46467, 46.32571],
    zoom: 14.5,
    interactive: false,
  });

  let route_layer_id = null;
  let markers = [];

  // Affichage des parcours déjà créés sur la carte avec défilement
  map.on("style.load", function () {
    fetch(url_api + "parcours/get_parcours.php")
      .then((response) => response.json())
      .then((data) => {
        // Connexion et récupération des données réussies
        console.log("Connexion à l'API validée. (map)");
        console.log("Récupération des données validée. (map)");
        
        if (data.parcours.length > 0) {
          const compteur_parcours = data.parcours.length;
          document.getElementById("value-parcours").textContent = compteur_parcours;

          // Fonction permettant d'afficher chaque parcours avec délais entre chaque 
          function afficher_parcours(index) {
            if (index >= compteur_parcours) {
              return;
            }

            const parcours = data.parcours[index];
            const coordonnees = parcours.points;
            const coordonnees_traces = coordonnees.map((coordonnee) => [
              coordonnee.longitude,
              coordonnee.latitude,
            ]);
            // Options d'affichage tracés + markers de couleurs rouge
            if (map.getLayer(route_layer_id)) {
              map.getSource("route").setData({
                type: "Feature",
                properties: {},
                geometry: {
                  type: "LineString",
                  coordinates: coordonnees_traces,
                },
              });
            } else {
              map.addSource("route", {
                type: "geojson",
                data: {
                  type: "Feature",
                  properties: {},
                  geometry: {
                    type: "LineString",
                    coordinates: coordonnees_traces,
                  },
                },
              });

              map.addLayer({
                id: "route",
                type: "line",
                source: "route",
                layout: {
                  "line-join": "round",
                  "line-cap": "round",
                },
                paint: {
                  "line-color": "#FF0000",
                  "line-width": 4,
                },
              });

              route_layer_id = "route";
            }

            markers.forEach((marker) => marker.remove());
            markers = [];

            coordonnees.forEach((coordonnee) => {
              const marker = new mapboxgl.Marker({ color: "red" })
                .setLngLat([coordonnee.longitude, coordonnee.latitude])
                .addTo(map);
              markers.push(marker);
            });

            setTimeout(function () {
              afficher_parcours(index + 1);
            }, 8000);
          }
          // Affichage du parcours
          afficher_parcours(0);
        } else {
          console.log("Aucun parcours trouvé. (map)");
        }
      })
      // Message d'erreur si probleme de récéption avec APi
      .catch((error) => {
        console.error("Erreur lors de la récupération des données :", error);
        console.log("Vérifier l'état de la connexion internet et l'adresse IP sur laquelle les données sont récupérées. (map)");
      });
  });
  return map;
}

/**
 * @brief Fonction affichant les derniers groupes créées de l'API
 */
function afficher_derniers_groupes_API() {
  fetch(url_api + "groupe/get_groupe.php")
    .then((response) => response.json())
    .then((data) => {

      // Connexion et récuperation des données réussi
      console.log("Connexion à l'API validée. (groupe)");
      console.log("Récuperation des données validée. (groupe)");

      // Calcul nombre de groupes enregistrés
      compteur_groupe = data.groupe.length;

      // filtre permettant de récuperer les infos de la table groupe (seulement les 3 derniers groupes créés)
      const groupes = data.groupe.slice(-3);
      const groupes_container = document.querySelector("#groupes-dashboard");

      // Création des box dynamiquement en js -> html pour chaque groupe
      groupes.forEach((data) => {
        // Création de la box dans la page html
        const groupe_box = document.createElement("div");
        groupe_box.classList.add("groupe-box-dashboard");

        // Ajout de l'image de groupe
        const image_groupe = document.createElement("img");
        image_groupe.src = data.avatar;
        image_groupe.classList.add("avatar-groupe-dashboard");
        groupe_box.appendChild(image_groupe);

        // Ajout div pour infos du groupe
        const info_groupe = document.createElement("div");
        info_groupe.classList.add("info-groupe-dashboard");

        // Ajout du nom de groupe
        const nom_groupe = document.createElement("p");
        nom_groupe.textContent = "Nom du groupe : " + data.nom;
        info_groupe.appendChild(nom_groupe);

        // Ajout du code pin de groupe
        const code_pin = document.createElement("p");
        code_pin.textContent = "Code pin : " + data.pin;
        info_groupe.appendChild(code_pin);
        // fin de div
        groupe_box.appendChild(info_groupe);

        groupes_container.appendChild(groupe_box);
      });

      // Affichage du nombre de groupe totale dans la db
      document.getElementById("value-groupe").textContent = compteur_groupe;
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données :", error);
      console.log("Vérifier l'état de la connexion internet et l'adresse IP sur laquelle les données sont récupérées. (groupe)");
    });
}

/**
 * @brief Fonction affichant le nombre de questions stockées dans la db
 */
function afficher_recap_questions_API() {
  fetch(url_api + "question/get_question.php")
    .then((reponse) => reponse.json())
    .then((donnees) => {

      // Connexion et récuperation des données réussi
      console.log("Connexion à l'API validée. (question)");
      console.log("Récuperation des données validée. (question)");

      compteur_questions = donnees.questions.length;
      const questions = donnees.questions.slice(-4); // récupère les 5 derniers éléments

      const tableau = document.querySelector("#questions-table tbody");

      questions.forEach((question) => {

        const ligne = document.createElement("tr");
        const cellule = document.createElement("td");

        // Crée un élément <span> pour l'identifiant de la question
        const id_span = document.createElement("span");
        id_span.textContent = question.id;
        id_span.classList.add("question-id"); // Ajoute une classe pour le style spécifique de l'identifiant

        // Ajoute l'identifiant et la question à la cellule
        cellule.appendChild(id_span);
        cellule.appendChild(document.createTextNode(`: ${question.question}`));

        // Ajoute la cellule à la ligne
        ligne.appendChild(cellule);

        // Ajoute la ligne au corps du tableau
        tableau.appendChild(ligne);
      });
      // Affichage du nombre de question totale dans la db
      document.getElementById("value-question").textContent = compteur_questions;
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données :", error);
      console.log("Vérifier l'état de la connexion internet et l'adresse IP sur laquelle les données sont récupérées. (question)");
    });
}

/**
 * @brief Fonction permettant de vider les tables vote et score
 */
function supprimer_votes_score_API() {
  // Supprimer tous les groupes de l'API
  fetch(url_api + "score/delete_all_score_vote.php", {
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
/**
 * @brief Fonction permettant de mettre à jour la table élève
 * @note Cette fonction traite le fichier excel contenant le nom des élève puis met sous forme d'identifiant : p.nom (ou p est la premiere lettre du prenom)
 * @author Charly Mercier
 */
function generer_identifiants() {
  const fileInput = document.getElementById('file_input');
  const file = fileInput.files[0];
  const reader = new FileReader();
  reader.onload = function (e) {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const worksheet = workbook.Sheets[workbook.SheetNames[0]];

      // Indices des colonnes
      const nomIndex = 0;
      const prenomIndex = 1;
      const identifiantIndex = 4;

      for (let row = 1; ; row++) {
          const nomCell = worksheet[XLSX.utils.encode_cell({ c: nomIndex, r: row })];
          const prenomCell = worksheet[XLSX.utils.encode_cell({ c: prenomIndex, r: row })];

          if (nomCell && nomCell.v && prenomCell && prenomCell.v) {

              const identifiant = `${prenomCell.v.charAt(0)}.${nomCell.v}`;
              worksheet[XLSX.utils.encode_cell({ c: identifiantIndex, r: row })] = { t: 's', v: identifiant };
              identifiants.push(identifiant);
              //console.log(identifiant);
          } else {
              break;
          }
      }
      const newWorkbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(newWorkbook, worksheet, 'Feuille1');
      const newWorkbookData = XLSX.write(newWorkbook, { type: 'array', bookType: 'xlsx' });

      const blob = new Blob([newWorkbookData], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', 'nouveau_fichier.xlsx');
      document.body.appendChild(link);
      link.click();

      //console.log(identifiants);
      const api_excel = url_api + 'eleve/post_eleve.php';
      if (identifiants.length > 0) {
          const data = identifiants.map(identifiant => ({
              identifiant: identifiant,
              autorisation: 1
          }));
          //console.log(data);
          const requestOptions = {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json'
              },
              body: JSON.stringify(data)
          };
          
          //console.log(requestOptions);

          fetch(api_excel, requestOptions)
              .then(response => response.json())
              .then(result => {
                  //console.log(result);
              })
              .catch(error => {
                  console.error('Error:', error);
              });
      } else {
          //console.log('Aucun identifiant à envoyer à l\'API.');
      }
  };
  reader.readAsArrayBuffer(file);
}

// Afficher le groupe en première position (code réalisé par Charly)
fetch(url_api + "score/classement_top1.php")
  .then(response => response.json())
  .then(data_top1 => {
    const div_gagnant = document.getElementById('value-gagnant');
    div_gagnant.innerHTML = data_top1.score[0].score_fk_groupe;
  })
  .catch(error => console.error(error));

