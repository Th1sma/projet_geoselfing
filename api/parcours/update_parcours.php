<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "PARCOURS"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet de mettre à jour la progression des joueurs selon les points de passage déjà effectués (0 si le point n'est pas encore fait par le groupe)
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
    $table_coordonnees = 'coordonnees';

    // Vérification des données recues
    if (!empty($donnees['id']) && isset($donnees['valide'])) {
        $id_coordonnees = $donnees['id'];
        $valide = $donnees['valide'];

        // Préparation de la requête SQL pour la mise à jour de la colonne "valide"
        $sql = "UPDATE $table_coordonnees SET valide = :valide WHERE id = :id";
        $stmt = $handle->prepare($sql);
        $stmt->bindValue(':id', $id_coordonnees);
        $stmt->bindValue(':valide', $valide);

        // Exécution de la requête SQL
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "La coordonnée a été mise à jour avec succès."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erreur lors de la mise à jour de la coordonnée."], JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "L'ID de la coordonnée ou la valeur 'valide' n'a pas été fournie."], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "POST"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>
