/**
 * @author Mathis NIVEAU 
 * @date   21/03/2023
 * @description Ce fichier est utilisé pour toutes les pages html du site
 * @version 1.0.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier comporte plusieurs fonctions qui permettant de faire fonctionner certains aspects du site
 * - Fonction show_modal()
 * - Fonction hide_modal()
 * - Fonction valider_formulaire(formulaire)
 */

var url_api = "http://192.168.0.10/api/";
localStorage.setItem('url_api', url_api);

/**
 * @brief Fonction pour afficher la boîte de dialogue modale
 */
function show_modal() {
  let modal = document.getElementById("modal");
  modal.style.display = "block";
  modal.classList.add("show-modal"); // Ajouter la classe show-modal
}

/**
 * @brief Fonction pour masquer la boîte de dialogue modale
 */
function hide_modal() {
  let modal = document.getElementById("modal");
  modal.style.display = "none";
  modal.classList.remove("show-modal"); // Enlever la classe show-modal
}

/**
 * @brief Fonction permettant de vérifier le remplissage totale des formulaires avant leurs envois
 * @param {formulaire} formulaire 
 * @returns TRUE / FALSE, si le formulaire est valide ou non
 */
function valider_formulaire(formulaire) {
  let valide = true;
  let champs = formulaire.querySelectorAll("input, select, textarea");
  let pattern = /^[A-Za-z0-9\-_\.?#éèà@]+$/; // Variable autorisant certains caractères

  // Boucle de parcours des inputs dans le formulaire
  champs.forEach(function (champ) {
    if (champ.hasAttribute("required") && champ.value === "") {
      valide = false;
      return;
    }
    if (champ.type === "file" && champ.accept === "image/png, image/jpeg") {
      return valide;
    }
    if (!pattern.test(champ.value)) {
      valide = false;
      alert("Les caractères entrés dans le champ " + champ.name + " ne sont pas autorisés.");
      // Vide le ou les champs avec des caractères non autorisés
      champ.value = "";
    }
  });
  return valide;
}

