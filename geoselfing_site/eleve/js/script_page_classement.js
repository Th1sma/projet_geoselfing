var url_api = localStorage.getItem('url_api');

// Afficher le groupe en première position
fetch(url_api + "score/classement_top1.php")
    .then(response => response.json())
    .then(data_top1 => {
        const resultatDiv = document.getElementById('top1');
        resultatDiv.innerHTML = `Groupe n° ${data_top1.score[0].score_fk_groupe}`;
    })
    .catch(error => console.error(error));


// Afficher le groupe en deuxième position
fetch(url_api + "score/classement_top2.php")
    .then(response => response.json())
    .then(data_top2 => {
        const resultatDiv = document.getElementById('top2');
        resultatDiv.innerHTML = `Groupe n° ${data_top2.score[0].score_fk_groupe}`;
    })
    .catch(error => console.error(error));


// Afficher le groupe en troisième position
fetch(url_api + "score/classement_top3.php")
    .then(response => response.json())
    .then(data_top3 => {
        const resultatDiv = document.getElementById('top3');
        resultatDiv.innerHTML = `Groupe n° ${data_top3.score[0].score_fk_groupe}`;
    })
    .catch(error => console.error(error));
