/**
 * @author Mathis NIVEAU 
 * @date   21/03/2023
 * @description Ce fichier est utilisé exclusivement pour la page contact.html
 * @version 1.0.0
 * @implements API Mailjet pour envoyer des messages depuis le formulaire
 * @implements REQUIS : installer le package emailJS pour le bon fonctionnement de l'envoie des données
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier comporte un fonction qui permettant de faire fonctionner l'intégralité de la page
 */

document.getElementById('formulaire-contact').addEventListener('submit', function(event) {
  event.preventDefault(); // Empêche la soumission du formulaire

  // Récupère les données du formulaire
  var nom = document.getElementById('nom').value;
  var email = document.getElementById('email').value;
  var message = document.getElementById('message').value;

  // Envoie les données à EmailJS
  emailjs.send("service_nxj3gwc", "template_c1xb8mc", {
    nom: nom,
    email: email,
    message: message

  }).then(function(response) {
    console.log("Formulaire envoyé", response.status, response.text);
    // Message qui s'affiche sur la page html accusé d'envoie
    alert("Le message a bien été envoyé au support technique !");
    location.reload();
    
  }, function(error) {
    console.log("Erreur lors de l'envoie :", error);
    alert("Le message n'a pas pu etre envoyé, vérifié votre connexion à internet !")
  });
});