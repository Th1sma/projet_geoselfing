<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "QUESTIONNAIRE"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet de supprimer une question de la DB 
 */

// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    // Appel de la base de données et des tables à modifier
    $handle = new SQLite3("../geoselfing.db");
    $table_questions = 'questions';
    $table_reponses = 'reponses';
    $table_question_reponse = 'question_reponse';

    // Extraction des données envoyées
    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données recues, Récupération des données
    if (!empty($donnees['id'])) {
        $id = $donnees['id'];

        // Suppression des réponses associées à la question dans la table intermédiaire "question_reponse"
        $sql_delete_question_reponse = "DELETE FROM $table_question_reponse WHERE question_id = :id";
        $stmt_delete_question_reponse = $handle->prepare($sql_delete_question_reponse);
        $stmt_delete_question_reponse->bindValue(':id', $id);
        $result_delete_question_reponse = $stmt_delete_question_reponse->execute();

        // Suppression des réponses associées à la question dans la table "reponses"
        $sql_delete_reponses = "DELETE FROM $table_reponses WHERE id IN (SELECT reponse_id FROM $table_question_reponse WHERE question_id = :id)";
        $stmt_delete_reponses = $handle->prepare($sql_delete_reponses);
        $stmt_delete_reponses->bindValue(':id', $id);
        $result_delete_reponses = $stmt_delete_reponses->execute();

        // Suppression de la question dans la table "questions"
        $sql_delete_question = "DELETE FROM $table_questions WHERE id = :id";
        $stmt_delete_question = $handle->prepare($sql_delete_question);
        $stmt_delete_question->bindValue(':id', $id);
        $result_delete_question = $stmt_delete_question->execute();

        // Vérification de la réussite de la suppression
        if ($result_delete_question && $result_delete_reponses && $result_delete_question_reponse) {
            http_response_code(201);
            echo json_encode(["message" => "La question et ses réponses associées ont été supprimées avec succès."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erreur lors de la suppression de la question et de ses réponses."], JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "L'identifiant envoyé est incorrect."], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "DELETE"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>
