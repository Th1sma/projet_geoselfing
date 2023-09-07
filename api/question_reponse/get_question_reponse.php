<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "QUESTIONNAIRE"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet d'afficher toutes les questions avec leurs réponses associées, incluant l'image de la question
 */

// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Appel de la base de données
$handle = new SQLite3("../geoselfing.db");
$table_questions = 'questions';
$table_reponses = 'reponses';
$table_question_reponse = 'question_reponse';

// Préparation de la requête SQL
$sql = "SELECT q.id, q.question, q.image, r.id as reponse_id, r.reponse, qr.reponse_correct 
        FROM $table_questions q
        INNER JOIN $table_question_reponse qr ON q.id = qr.question_id 
        INNER JOIN $table_reponses r ON qr.reponse_id = r.id";

$stmt = $handle->prepare($sql);
$result = $stmt->execute();

// Vérification du résultat
if ($result) {
  $questions = array();

  // Récupération des données dans un tableau
  while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $question_id = $row['id'];
    $reponse_id = $row['reponse_id'];
    $reponse_correcte = $row['reponse_correct'];

    // Vérifier si la question existe déjà dans le tableau
    if (!isset($questions[$question_id])) {
      // Si la question n'existe pas encore dans le tableau, l'ajouter avec ses données
      $questions[$question_id] = array(
        'id' => $question_id,
        'image' => $row['image'],
        'question' => $row['question'],
        'reponses' => array()
      );
    }

    // Ajouter la réponse associée à la question
    $questions[$question_id]['reponses'][] = array(
      'id' => $reponse_id,
      'reponse' => $row['reponse'],
      'reponse_correct' => ($reponse_correcte == 1)
    );
  }

  // Construction du tableau final
  $data = array(
    'questions' => array_values($questions)
  );

  // Envoi de la réponse au format JSON
  echo json_encode($data, JSON_PRETTY_PRINT);

} else {
  http_response_code(500);
  echo json_encode(['message' => 'Erreur lors de la récupération des données.'], JSON_UNESCAPED_UNICODE);
}
?>
