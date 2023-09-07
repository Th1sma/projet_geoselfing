<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "QUESTIONNAIRE"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet de supprimer tout le questionnaire de la DB API
 */

// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/**
 * Supprime toutes les images dans le dossier questions_img, sauf l'image "image_defaut.png"
 */
function supprimer_images_questions() {
    $dossier = '../questions_img/';

    foreach (glob($dossier . '*.png') as $fichier) {
        if (basename($fichier) !== 'image_defaut.png') {
            unlink($fichier);
        }
    }
}

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

  // Appel de la database et de la table à modifier
  $handle = new SQLite3("../geoselfing.db");
  $table_questions = 'questions';
  $table_reponses = 'reponses';
  $table_question_reponse = 'question_reponse';

  // Extraction des données envoyées
  $donnees = json_decode(file_get_contents("php://input"), true);

  // Vérification des données recues
  if (!empty($donnees['all'])) {

    // Activation de l'écriture du schéma
    $handle->exec("PRAGMA writable_schema = ON;");

    // Suppression des entrées dans sqlite_sequence
    $handle->exec("DELETE FROM sqlite_sequence WHERE name IN ('$table_questions', '$table_reponses', '$table_question_reponse');");

    // Désactivation de l'écriture du schéma et VACUUM
    $handle->exec("PRAGMA writable_schema = OFF; VACUUM;");

    // Suppression des données de chaque table séparément
    $handle->exec("DELETE FROM '$table_questions';");
    $handle->exec("DELETE FROM '$table_reponses';");
    $handle->exec("DELETE FROM '$table_question_reponse';");

    // Suppression des photos dans le dossier questions_img
    supprimer_images_questions();

    // Vérification du nombre de lignes affectées
    $rowCount = $handle->changes();

    if ($rowCount > 0) {
      http_response_code(201);
      echo json_encode(["message" => "Toutes les données ont été supprimées avec succès."], JSON_UNESCAPED_UNICODE);
    } else {
      http_response_code(500);
      echo json_encode(["message" => "Erreur lors de la suppression des données."], JSON_UNESCAPED_UNICODE);
    }
  } else {
    http_response_code(400);
    echo json_encode(["message" => "Erreur dans la requete."], JSON_UNESCAPED_UNICODE);
  }
} else {
  // Si la méthode n'est pas "DELETE"
  http_response_code(405);
  echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>