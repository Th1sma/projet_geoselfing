<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "GROUPE"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet de supprimer tout les groupes enregistrés dans l'API
 */

// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/**
 * @Brief Fonction permettant de supprimer toutes les photos et fichiers des groupes
 */
function supprimer_contenu_dossier($chemin_dossier)
{
    $fichiers = glob($chemin_dossier . '/*'); // Récupère tous les fichiers et dossiers du dossier
    foreach ($fichiers as $fichier) {
        if (is_dir($fichier)) {
            // Si c'est un dossier, appelle récursivement la fonction pour supprimer son contenu
            supprimer_contenu_dossier($fichier);
            // Supprimer le dossier lui-même
            rmdir($fichier);
        } else {
            // Si c'est un fichier, le supprime
            unlink($fichier);
        }
    }
}

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    $handle = new SQLite3("../geoselfing.db");
    $table = 'groupe';

    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données recues, Récupération des données
    if (!empty($donnees['all'])) {

        // Activation de l'écriture du schéma
        $handle->exec("PRAGMA writable_schema = ON;");

        // Suppression des entrées dans sqlite_sequence
        $handle->exec("DELETE FROM sqlite_sequence WHERE name IN ('$table');");

        // Désactivation de l'écriture du schéma et VACUUM
        $handle->exec("PRAGMA writable_schema = OFF; VACUUM;");

        // Suppression des données de chaque table séparément
        $handle->exec("DELETE FROM $table;");

        $chemin_dossier_photos = "../groupes_img/";
        // Supprimer le contenu du dossier "groupes_img"
        supprimer_contenu_dossier($chemin_dossier_photos);

        $chemin_dossier_avatar = "../groupes_avatar/";
        // Supprimer le contenu du dossier "avatar_groupe"
        supprimer_contenu_dossier($chemin_dossier_avatar);
    
        // Vérification du nombre de lignes affectées
        $rowCount = $handle->changes();

        // Exécution de la requete SQL
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