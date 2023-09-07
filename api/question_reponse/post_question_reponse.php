<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "QUESTIONNAIRE"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet d'enregistrer une nouvelle question avec sa photo ou non
 */

// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Appel de la base de données
    $handle = new SQLite3("../geoselfing.db");
    $table_question_reponse = 'question_reponse';
    $table_questions = 'questions';
    $table_reponses = 'reponses';

    // Extraction des données envoyées
    $donnees = json_decode(file_get_contents('php://input'), true);

    // Vérification des données reçues et récupération des données
    if (!empty($donnees['question']) && !empty($donnees['reponses'])) {
        $question = $donnees['question'];
        $reponses = $donnees['reponses'];
        $image_base64 = $donnees['image'];

        // Vérification si la question existe déjà
        // Vérifie que la question existe déjà, elle s'arrête lorsqu'elle trouve une question avec le même nom existant
        $sql_check_question = "SELECT COUNT(*) AS count FROM $table_questions WHERE question = :question";
        $stmt_check_question = $handle->prepare($sql_check_question);
        $stmt_check_question->bindValue(':question', $question);
        $result_check_question = $stmt_check_question->execute()->fetchArray(SQLITE3_ASSOC);

        if ($result_check_question['count'] > 0) {
            http_response_code(400);
            echo json_encode(["message" => "Le nom de la question existe déjà."], JSON_UNESCAPED_UNICODE);
            exit; // Arrêter l'exécution du script si la question existe déjà
        }

        // Début de la transaction
        $handle->exec('BEGIN');

        if (isset($image_base64) && trim($image_base64) !== '') {
            // Insertion de la question sans l'image
            $stmt_question = $handle->prepare("INSERT INTO $table_questions (question) VALUES (:question)");
            $stmt_question->bindValue(':question', $question);
            $stmt_question->execute();

            // Obtention de l'ID de la question insérée
            $id_question = $handle->lastInsertRowID();

            // Chemin de l'image avec l'ID de la question
            $chemin_image = "../questions_img/q" . $id_question . "_image.png";

            // Décode l'image en base64 et la sauvegarde dans le dossier "questions_img"
            $image_question = base64_decode($image_base64);
            file_put_contents($chemin_image, $image_question);

            // Met à jour le champ image dans la table questions avec l'URL de l'image
            $stmt_update_image = $handle->prepare("UPDATE $table_questions SET image = :image WHERE id = :id");
            $stmt_update_image->bindValue(':image', "http://192.168.0.10/api/questions_img/q" . $id_question . "_image.png");
            $stmt_update_image->bindValue(':id', $id_question);
            $stmt_update_image->execute();
        } else {
            // Requete si il n'y a pas d'image inserée
            $stmt_question = $handle->prepare("INSERT INTO $table_questions (question) VALUES (:question)");
            $stmt_question->bindValue(':question', $question);
            $stmt_question->execute();

            // Obtention de l'ID de la question insérée
            $id_question = $handle->lastInsertRowID();
        }

        // Insertion des réponses
        foreach ($reponses as $reponse) {
            $stmt_reponse = $handle->prepare("INSERT INTO $table_reponses (reponse) VALUES (:reponse)");
            $stmt_reponse->bindValue(':reponse', $reponse['reponse']);
            $stmt_reponse->execute();
            $id_reponse = $handle->lastInsertRowID();

            // Insertion de la relation question-réponse
            $stmt_question_reponse = $handle->prepare("INSERT INTO $table_question_reponse (question_id, reponse_id, reponse_correct) VALUES (:question_id, :reponse_id, :reponse_correct)");
            $stmt_question_reponse->bindValue(':question_id', $id_question);
            $stmt_question_reponse->bindValue(':reponse_id', $id_reponse);
            $stmt_question_reponse->bindValue(':reponse_correct', $reponse['reponse_correct']);
            $stmt_question_reponse->execute();
        }

        // Fin de la transaction
        $handle->exec('COMMIT');

        http_response_code(201);
        echo json_encode(['message' => 'La question et ses réponses ont été ajoutées avec succès.'], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Les données envoyées sont incomplètes.'], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "POST"
    http_response_code(405);
    echo json_encode(['message' => 'La méthode n\'est pas autorisée.'], JSON_UNESCAPED_UNICODE);
}
?>
