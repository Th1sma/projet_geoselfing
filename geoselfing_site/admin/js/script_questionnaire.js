/**
 * @author Mathis NIVEAU
 * @date   11/05/2023
 * @description Ce fichier est utilisé exclusivement pour la page contact.html
 * @version 1.2.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier comporte plusieurs fonctions qui permettant de faire fonctionner l'intégralité de la page
 * - Fonction afficher_questions_API()
 * - Fonction traitement_image_question()
 * - Fonction envoyer_question_API(form)
 * - Fonction recherche_question()
 * - Fonction scroll_vers_question(question_id)
 * - Fonction scroll_header()
 * - Fonction valider_formulaire_questionnaire(formulaire)
 */

// url de l'api pour faciliter le changement d'adresse IP vers API
var url_api = localStorage.getItem('url_api');

// Variable image : avatar de chaque groupe en base64
var image_base64 = "";

/**
 * @brief Fonction permettant d'afficher 
 */
function afficher_questions_API() {
  // Appel à l'API pour récupérer les questions
  fetch(url_api + "question_reponse/get_question_reponse.php")
    .then((response) => response.json())
    .then((data) => {

      // Connexion et récupération des données réussies
      console.log("Connexion à l'API validée.");
      console.log("Récupération des données validée.");

      // Affichage du nombre de questions dans la partie récap
      let questions = data.questions;
      let nombre_questions = questions.length;
      document.getElementById("nombre-question").textContent = nombre_questions;

      // Construction du formulaire dynamique
      const conteneur_questions = document.querySelector("#container_questions");

      // Parcours des questions
      questions.forEach((question) => {
        const nouvelle_question = document.createElement("div");
        nouvelle_question.classList.add("conteneur_question");

        const image_question = document.createElement("img");
        image_question.src = question.image;
        image_question.classList.add("image-question");

        const label_question = document.createElement("label");
        label_question.textContent = `Question n°${question.id}:`;

        // Création de l'input question
        const input_question = document.createElement("input");
        input_question.setAttribute("type", "text");
        input_question.setAttribute("name", `question_${question.id}`);
        input_question.setAttribute("readonly", "readonly");
        input_question.value = question.question;

        nouvelle_question.appendChild(image_question);
        nouvelle_question.appendChild(label_question);
        nouvelle_question.appendChild(input_question);
        

        // Parcours des réponses de la question
        question.reponses.forEach((reponse, i) => {
          const nouvelle_reponse = document.createElement("div");
          nouvelle_reponse.classList.add("conteneur_reponse");

          const label_reponse = document.createElement("label");
          label_reponse.textContent = `Réponse ${i + 1}:`;

          const input_reponse = document.createElement("input");
          input_reponse.setAttribute("type", "text");
          input_reponse.setAttribute("name", `reponse_${question.id}_${i + 1}`);
          input_reponse.setAttribute("readonly", "readonly");
          input_reponse.value = reponse.reponse;

          // Création de la checkbox pour valider ou non la réponse
          const checkbox_correct = document.createElement("input");
          checkbox_correct.setAttribute("type", "checkbox");
          checkbox_correct.setAttribute("name", `correct_${question.id}`);
          checkbox_correct.setAttribute("value", `${i + 1}`);
          checkbox_correct.checked = reponse.reponse_correct;
          checkbox_correct.disabled = true;

          nouvelle_reponse.appendChild(label_reponse);
          nouvelle_reponse.appendChild(input_reponse);
          nouvelle_reponse.appendChild(checkbox_correct);
          nouvelle_question.appendChild(nouvelle_reponse);
        });

        // Création du bouton de suppression
        const bouton_supprimer = document.createElement("button");
        bouton_supprimer.textContent = "Supprimer";
        bouton_supprimer.id = "supprimer-question";
        bouton_supprimer.setAttribute("type", "button");
        bouton_supprimer.setAttribute("data-question-id", question.id);

        // Fonction permettant de supprimer la question et ses réponses 
        bouton_supprimer.addEventListener("click", function () {
          supprimer_question_API(question.id);
          nouvelle_question.remove();
        });
        // Barre de séparation entres les questions
        const hr = document.createElement("hr");
        hr.id = "barre-separation";

        nouvelle_question.appendChild(bouton_supprimer);
        nouvelle_question.appendChild(hr);

        conteneur_questions.appendChild(nouvelle_question);
      });
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des données :", error);
      console.log("Vérifier l'état de la connexion internet et l'adresse IP sur laquelle les données sont récupérées.");
    });
}

/**
 * @brief Fonction permettant de transformer une image en BASE64 pour envoie json vers appli
 * @returns L'image chiffrer en base64
 */
function traitement_image_question() {
  const image_input = document.getElementById("input-image-question");
  if (image_input.files.length === 0) {
    return null; // Aucun fichier sélectionné
  }

  const image = image_input.files[0];
  const reader = new FileReader();
  reader.readAsDataURL(image);
  reader.onload = function () {
    image_base64 = reader.result.split(',')[1];
    console.log("La conversion en base64 a été effectuée");
    // Ici, vous pouvez appeler une fonction pour envoyer l'image_base64 à l'API, si nécessaire
  }
  return image_base64;
}


/**
 * @brief Fonction permettant d'envoyer une question à l'API
 * @param {formulaire} form récuperation du formulaire dans la page html 
 */
function envoyer_question_API(form) {
  var image_question = traitement_image_question();
  // Création de l'objet contenant les données
  const donnees = {
    question: form.question.value,
    image: image_question,
    reponses: [
      {
        reponse: form.reponse_1.value,
        reponse_correct: form.correct_1.checked ? 1 : 0,
      },
      {
        reponse: form.reponse_2.value,
        reponse_correct: form.correct_2.checked ? 1 : 0,
      },
      {
        reponse: form.reponse_3.value,
        reponse_correct: form.correct_3.checked ? 1 : 0,
      },
      {
        reponse: form.reponse_4.value,
        reponse_correct: form.correct_4.checked ? 1 : 0,
      },
    ],
  };
  console.log(donnees);
  // Envoi de la requête à l'API avec la méthode POST
  fetch(url_api + "question_reponse/post_question_reponse.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(donnees),
  })
    // Affichage des messages erreurs ou non
    .then(function (response) {
      if (response.ok) {
        console.log("Les données ont été envoyées avec succès !");
        location.reload();
      } else {
        response.json().then(function (data) {
          console.log("Une erreur s'est produite lors de l'envoi de la question et de ses réponses.", data.message);
          alert(data.message);
        });
      }
    })
    .catch((error) => {
      console.error("Erreur : ", error);
    });
}

/**
 * @brief Fonction permettant de rechercher une question dans la db
 */
function recherche_question_API() {
  let input = document.getElementById("recherche-input").value.toLowerCase();

  // Vérification avec les données de l'API pour voir si il y a des correspondances avec l'input
  fetch(url_api + `question/get_question.php?search=${input}`)
    .then(response => response.json())
    .then(data => {

      let questions = data.questions;
      let resultat = document.getElementById("recherche-resultat");
      resultat.innerHTML = "";

      if (questions.length > 0) {
        for (let i = 0; i < questions.length; i++) {
          let question = questions[i].question;
          let question_id = questions[i].id;

          // Si il y a quelques choses, création de la li avec la question à l'intérieur
          let li = document.createElement("li");
          li.textContent = question;
          li.setAttribute("data-question-id", question_id);
          li.addEventListener("click", function () {
            scroll_vers_question(question_id);
          });

          resultat.appendChild(li);
        }
      } else {
        let li = document.createElement("li");
        li.textContent = "Aucune question n'a été trouvée !";
        resultat.appendChild(li);
      }
    })
    // Si aucune question trouvée dans la db de l'API
    .catch(error => {
      let resultat = document.getElementById("recherche-resultat");
      resultat.innerHTML = "";

      let li = document.createElement("li");
      li.textContent = "Aucune question n'a été trouvée !";
      resultat.appendChild(li);

      console.log("Erreur lors de la recherche :", error);
    });
}

/**
 * @brief Fonction permettant de supprimer une question du questionnaire 
 * @param {nombre} id Id de la question voulant etre supprimée
 */
function supprimer_question_API(id) {
  // Formatage JSON pour l'envoi de l'ID de la question à supprimer
  let data = {
    id: id
  };
  // Suppression de la question
  fetch(url_api + "question_reponse/delete_question.php", {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
    // Affichage des messages erreurs ou non
    .then((response) => {
      if (response.ok) {
        console.log("Les données ont été supprimées avec succès !");
      } else {
        response.json().then(function (data) {
          console.log("Une erreur s'est produite lors de la suppression de la question et de ses réponses.", data.message);
        });
      }
    })
    .catch((error) => {
      console.error("Erreur : ", error);
    });
}

/**
 * @brief Fonction permettant de supprimer tout le questionnaire
 */
function supprimer_questionnaire_API() {
  // Supprimer tous les questions de la page HTML
  let question_container = document.querySelectorAll(".conteneur_question");
  question_container.forEach((question_container) => {
    question_container.remove();
  });
  // Supprimer tous le questionnaire de l'API
  fetch(url_api + "question_reponse/delete_all_question_reponse.php", {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ all: true })
  })
    // Affichage des messages erreurs ou non
    .then((response) => {
      if (response.ok) {
        console.log("Les données ont été supprimées avec succès !");
      } else {
        response.json().then(function (data) {
          console.log("Une erreur s'est produite lors de la suppression du questionnaire.", data.message);
        });
      }
    })
    .catch((error) => {
      console.error("Erreur : ", error);
    });
}

/**
 * @brief Fonction permettant de scroll vers la bonne question (fonctionne avec la fonction recherche)
 * @param {nombre} question_id id de la question vers laquelle aller
 */
function scroll_vers_question(question_id) {
  let question_elements = document.getElementsByTagName('label');

  // Parcours des questions dans la page pour trouver la bonne question dans la page
  for (let i = 0; i < question_elements.length; i++) {
    let question_element = question_elements[i];
    let label_text = question_element.textContent.trim();

    // Si c'est la bonne question, scroll vers sa position dans la page html
    if (label_text.startsWith(`Question n°${question_id}:`)) {
      question_element.scrollIntoView({ behavior: 'smooth' });
    }
  }
}

/**
 * @Brief Fonction qui permet de scroller vers le haut de page (page questionnaire) bouton scroll
 */
function scroll_header() {
  let headerElement = document.getElementById('header');
  if (headerElement) {
    headerElement.scrollIntoView({ behavior: 'smooth' });
  }
}

/**
 * @brief Fonction permettant de vérifier les valeurs entrées dans les input avant envoi
 * @param {formulaire} formulaire 
 * @returns Un booléan vrai ou faux pour vérifie que les entrées
 */
function valider_formulaire_questionnaire(formulaire) {
  let valide = true;
  let champs = formulaire.querySelectorAll("input, select, textarea");

  // Tableau pour stocker les cases à cocher
  let cases_a_cocher = [];

  champs.forEach(function (champ) {
    if (champ.hasAttribute("required") && champ.value === "") {
      valide = false;
      return;
    }
    // Vérifier si le champ est une case à cocher (SPECIAL QUESTIONNAIRE)
    if (champ.type === "checkbox") {
      cases_a_cocher.push(champ);
    }
  });

  // Vérifier si plus d'une case à cocher est cochée
  let cases_cochees = cases_a_cocher.filter(function (case_a_cocher) {
    return case_a_cocher.checked;
  });
  // Si il y a plus de 1 cases cochées 
  if (cases_cochees.length > 1) {
    cases_cochees.forEach(function (case_cochee) {
      case_cochee.checked = false;
    });
    valide = false;
    alert("Vous ne pouvez sélectionner qu'une seule option.");
  }

  if(cases_cochees.length < 1){
    valide = false;
    alert("Vous devez sélectionner une option.");
  }

  if (!valide) {
    alert("Veuillez remplir tous les champs obligatoires.");
  }
  return valide;
}


