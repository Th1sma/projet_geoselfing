<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "QUESTIONNAIRE"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet d'afficher les questions stockées dans la DB (utilisé dans la fonction de recherche sur le site coté administrateur)
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

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Lecture du paramètre de requête search
    $search_term = isset($_GET['search']) ? $_GET['search'] : null;

    // Préparation de la requête SQL
    $sql = "SELECT * FROM $table_questions";
    if ($search_term !== null) {
        $sql .= " WHERE question LIKE '%$search_term%'";
    }

    $result = $handle->query($sql);

    // Vérification du résultat
    if ($result !== false) {
        $questions = array();

        // Récupération des données dans un tableau
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $question_id = $row['id'];

            // Ajouter la question au tableau
            $questions[] = array(
                'id' => $question_id,
                'question' => $row['question']
            );
        }

        // Construction du tableau final
        $data = array(
            'questions' => $questions
        );

        // Envoi de la réponse au format JSON
        echo json_encode($data, JSON_PRETTY_PRINT);

    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Erreur lors de la récupération des données.'], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "GET"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>