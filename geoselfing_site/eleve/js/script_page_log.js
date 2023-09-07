var url_api = localStorage.getItem('url_api');

// Login page élève
function redirec() {

  var input = document.getElementById("username");
  var identifiant_entre = input.value;

  // Enregistrement de la variable identifiant_entre dans le stockage local
  localStorage.setItem('identifiant_entre', identifiant_entre);

  const identification = {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json;charset=UTF-8'
    },
    body: JSON.stringify({
      identifiant: identifiant_entre,
      autorisation: 1
    })
  };
  fetch(url_api + "eleve/log_eleve.php", identification)
    .then(response => {
      console.log(response);
      if (response.status === 201) {
        window.location.replace("./eleve/pages/page_vote.php");
      } else {
        alert("Vous avez déjà voté ou votre identifiant incorrect.");
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
}

