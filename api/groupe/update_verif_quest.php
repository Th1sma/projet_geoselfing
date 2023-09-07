<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "GROUPE"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet d'enregistrer la progression du questionnaire pour chaque groupe (0 si questionnaire non terminé)
 */

// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Extraction des données envoyées
$donnees = json_decode(file_get_contents("php://input"), true);

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Appel de la base de données
    $handle = new SQLite3("../geoselfing.db");
    $table_groupe = 'groupe';

    // Vérification des données recues
    if (!empty($donnees['id']) && isset($donnees['groupe_verif_questionnaire'])) {
        $id_groupe = $donnees['id'];
        $groupe_verif_questionnaire = $donnees['groupe_verif_questionnaire'];

        // Préparation de la requête SQL pour la mise à jour du champ "groupe_verif_questionnaire"
        $sql = "UPDATE $table_groupe SET groupe_verif_questionnaire = :groupe_verif_questionnaire WHERE id = :id";
        $stmt = $handle->prepare($sql);
        $stmt->bindValue(':id', $id_groupe);
        $stmt->bindValue(':groupe_verif_questionnaire', $groupe_verif_questionnaire);

        // Exécution de la requête SQL
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Le champ 'groupe_verif_questionnaire' a été mis à jour avec succès."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erreur lors de la mise à jour du champ 'groupe_verif_questionnaire'."], JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "L'ID du groupe ou la valeur 'groupe_verif_questionnaire' n'a pas été fournie."], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "POST"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>
