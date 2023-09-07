<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "PARCOURS"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet de supprimer tous les parcours enregistrés sur la DB de l'API
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

    // Vérification des données recues
    if (!empty($donnees['all'])) {

        // Activation de l'écriture du schéma
        $handle->exec("PRAGMA writable_schema = ON;");

        // Suppression des entrées dans sqlite_sequence
        $handle->exec("DELETE FROM sqlite_sequence WHERE name IN ('$table_parcours', '$table_coordonnees');");

        // Réinitialisation des clés étrangères dans la table "groupe"
        $handle->exec("UPDATE $table_groupe SET groupe_fk_parcours = 0;");

        // Désactivation de l'écriture du schéma et VACUUM
        $handle->exec("PRAGMA writable_schema = OFF; VACUUM;");

        // Suppression des données de chaque table séparément
        $handle->exec("DELETE FROM '$table_parcours';");
        $handle->exec("DELETE FROM '$table_coordonnees';");

        // Vérification du nombre de lignes affectées
        $rowCount = $handle->changes();

        if ($rowCount > 0) {
            http_response_code(201);
            echo json_encode(["message" => "Toutes les données ont été supprimées avec succès et les clés étrangères ont été réinitialisées."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erreur lors de la suppression des données."], JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Erreur dans la requête."], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "DELETE"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>