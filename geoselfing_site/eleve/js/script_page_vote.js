/* Récupération de la variable identifiant_entre depuis le stockage local */
var identifiant_entre = localStorage.getItem('identifiant_entre');

if (identifiant_entre) {
    console.log(identifiant_entre);
} else {
    console.log("La variable identifiant_entre n'est pas définie.");
}

var url_api = localStorage.getItem('url_api');

/* Récupère le nombre de groupe dans l'API */
let nb_groupe;

fetch(url_api + 'groupe/compter_groupe.php')
    .then(response => response.json())
    .then(data => {
        nb_groupe = data.groupe[0].total;
        processData(nb_groupe);
    })
    .catch(error => console.error(error));


/* Affichage des divs modales + appel fonction notation */
function processData(nb_groupe) {
    for (let i = 1; i <= nb_groupe; i++) {

        // Variables des boutons pour ouvrir les modales
        var bouton = document.querySelector('.bouton' + i);

        // Variables des boutons pour fermer les modales
        var close = document.querySelector('.close_g' + i);

        bouton.addEventListener('click', function () {
            showModal("modal_g" + i);
        });
        close.addEventListener('click', function () {
            hideModal("modal_g" + i);
        });
    }
}

/* Fonctions fenetre modal */
function showModal(modalId) {
    var modal = document.getElementById(modalId);
    modal.style.display = "block";
    modal.classList.add("show-modal");
}
function hideModal(modalId) {
    var modal = document.getElementById(modalId);
    modal.style.display = "none";
    modal.classList.remove("show-modal");
}

// ---------------------------------------------------------------------------------
// PARTIE VOTE ET CALCUL DE SCORE
/* Vérification si 'score' est vide sinon l'initialiser à 1 */
fetch(url_api + "score/get_score.php")
    .then(response => {
        if (response.status === 200) {
            return response.json();
        } else {
            throw new Error("La requête a échoué avec le code : " + response.status);
        }
    })
    .then(data => {
        if (data.hasOwnProperty('score') && data.score.length > 0) {
            return response.json();
        } else {
            initialisation_score();
        }
        console.log(data.message);
    })
    .catch(error => {
        console.error("La table n'est pas vide !," + error);
    });


let notes = [];
let fk = [];

/* Notation */
function submitRating() {

    for (let i = 1; i <= nb_groupe; i++) {

        let note = document.querySelector('input[name="rate' + i + '"]:checked').value;
        notes.push(note);
        fk.push(i);
    }


    // POST des notes de chaque groupe 
    const post_vote = url_api + "vote/post_vote.php";

    const chaine_note = {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json;charset=UTF-8'
        },
        body: JSON.stringify({
            votes: notes,
            vote_fk_groupe: fk
        })
    };

    fetch(post_vote, chaine_note)
        .then(response => {
            console.log("vote : " + response.status);
            recalcul_score();
            update_identifiant_entre();

            // Redirection après le vote
            window.location.replace("../../index.html");
        });
}


/* Initialisation du score (sert à éviter les bugs quand la table est vide) */
function initialisation_score() {
    const init_score = url_api + "score/post_score.php";

    const scoresData = [
        { scores: 1, score_fk_groupe: 1 },
        { scores: 1, score_fk_groupe: 2 },
        { scores: 1, score_fk_groupe: 3 },
        { scores: 1, score_fk_groupe: 4 },
        { scores: 1, score_fk_groupe: 5 },
        { scores: 1, score_fk_groupe: 6 },
        { scores: 1, score_fk_groupe: 7 },
        { scores: 1, score_fk_groupe: 8 },
        { scores: 1, score_fk_groupe: 9 },
        { scores: 1, score_fk_groupe: 10 }
    ];

    const options = {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(scoresData)
    };

    fetch(init_score, options)
        .then(response => response.json())
        .then(responseData => {
            console.log("Score : " + responseData.message);
        })
        .catch(error => {
            console.error("Erreur lors de la requête : " + error);
        });
}


/* Recalculer les scores */
function recalcul_score() {
    const calculer_score = url_api + "score/actualise_score.php";

    const score = {
        scores: null,
        score_fk_groupe: fk
    };

    const options = {
        method: "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(score)
    };

    fetch(calculer_score, options)
        .then(response => response.json())
        .then(responseData => {
            console.log("Score : " + responseData.message);
        })
        .catch(error => {
            console.error("Erreur lors de la requête : " + error);
        });
}


/* Mise à jour de l'autorisation après le vote */
function update_identifiant_entre() {
    var mettre_a_jour_autorisation = url_api + 'eleve/update_eleve.php';

    var data = {
        identifiant: identifiant_entre
    };

    var requestOptions = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    };

    fetch(mettre_a_jour_autorisation, requestOptions)
        .then(response => response.json())
        .then(result => {
            console.log(result);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}