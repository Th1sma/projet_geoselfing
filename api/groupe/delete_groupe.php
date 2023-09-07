<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "GROUPE"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet de supprimer un groupe enregistré dans l'API
 */

// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function supprimer_contenu_dossier($chemin_dossier)
{
    $fichiers = glob($chemin_dossier . '/*'); // Récupère tous les fichiers et dossiers du dossier

    foreach ($fichiers as $fichier) {
        if (is_dir($fichier)) {
            // Si c'est un dossier, appelle récursivement la fonction pour supprimer son contenu
            supprimer_contenu_dossier($fichier);
        } else {
            // Si c'est un fichier, le supprime
            unlink($fichier);
        }
    }

    // Supprime le dossier une fois que son contenu a été supprimé
    rmdir($chemin_dossier);
}

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    $handle = new SQLite3("../geoselfing.db");
    $table = 'groupe';

    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données recues, Récupération des données
    if (!empty($donnees['id'])) {
        $id = $donnees['id'];

        // Récupération du nom du groupe à partir de l'ID pour supprimer son dossier associé
        $sql = "SELECT nom FROM $table WHERE id = :id";
        $stmt = $handle->prepare($sql);
        $stmt->bindValue(':id', $id);
        $result = $stmt->execute();

        if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $nom_groupe = $row['nom'];

            $chemin_dossier_parent = "../groupes_img/";
            $chemin_dossier_groupe = $chemin_dossier_parent . $nom_groupe;

            // Vérifier si le dossier du groupe existe
            if (is_dir($chemin_dossier_groupe)) {
                // Supprimer le contenu du dossier du groupe
                supprimer_contenu_dossier($chemin_dossier_groupe);

                // Suppression des données du groupe dans la base de données
                $sql = "DELETE FROM $table WHERE id = :id";
                $stmt = $handle->prepare($sql);
                $stmt->bindValue(':id', $id);

                // Exécution de la requête SQL de suppression
                if ($stmt->execute()) {
                    http_response_code(201);
                    echo json_encode(["message" => "Les données ont été supprimées avec succès."], JSON_UNESCAPED_UNICODE);
                    echo json_encode(["message" => "Le dossier a été supprimé avec succès."], JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Erreur lors de la suppression du dossier."], JSON_UNESCAPED_UNICODE);
                }
            } else {
                http_response_code(402);
                echo json_encode(["message" => "Le dossier n'existe pas."], JSON_UNESCAPED_UNICODE);
            }
        } else {
            http_response_code(403);
            echo json_encode(["message" => "Le groupe correspondant à l'ID fourni n'a pas été trouvé."], JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "L'identifiant envoyé est mauvais."], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "DELETE"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>