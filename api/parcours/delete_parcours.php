<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "PARCOURS"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet de supprimer un parcours de la DB API
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
    $table_parcours = 'parcours';
    $table_coordonnees = 'coordonnees';
    $table_groupe = 'groupe';

    // Extraction des données envoyées
    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données recues, Récupération des données
    if (!empty($donnees['id'])) {
        $id = $donnees['id'];

        // Suppression des points associés au parcours dans la table "coordonnees"
        $sql_delete_points = "DELETE FROM $table_coordonnees WHERE parcours_id = :id";
        $stmt_delete_points = $handle->prepare($sql_delete_points);
        $stmt_delete_points->bindValue(':id', $id);
        $result_delete_points = $stmt_delete_points->execute();

        // Mise à jour de la clé étrangère dans la table "groupe"
        $sql_update_groupe = "UPDATE $table_groupe SET groupe_fk_parcours = 0 WHERE groupe_fk_parcours = :id";
        $stmt_update_groupe = $handle->prepare($sql_update_groupe);
        $stmt_update_groupe->bindValue(':id', $id);
        $result_update_groupe = $stmt_update_groupe->execute();

        // Suppression du parcours dans la table "parcours"
        $sql_delete_parcours = "DELETE FROM $table_parcours WHERE id_parcours = :id";
        $stmt_delete_parcours = $handle->prepare($sql_delete_parcours);
        $stmt_delete_parcours->bindValue(':id', $id);
        $result_delete_parcours = $stmt_delete_parcours->execute();

        // Vérification de la réussite de la suppression
        if ($result_delete_parcours && $result_delete_points && $result_update_groupe) {
            http_response_code(201);
            echo json_encode(["message" => "Le parcours, ses points associés et les groupes associés ont été supprimés avec succès."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erreur lors de la suppression du parcours, de ses points ou de la mise à jour des groupes."], JSON_UNESCAPED_UNICODE);
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
