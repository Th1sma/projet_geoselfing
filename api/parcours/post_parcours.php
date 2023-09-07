<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "PARCOURS"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet d'ajouter un parcours dans la DB
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

    // Extraction des données envoyées
    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données recues et récupération des valeurs
    if (!empty($donnees['nom_parcours']) && !empty($donnees['points']) && !empty($donnees['nom_groupe'])) {
        $nom_parcours = $donnees['nom_parcours'];
        $points = $donnees['points'];
        $nom_groupe = $donnees['nom_groupe'];

        // Insertion du parcours dans la table "parcours" si le nom n'existe pas déjà
        // Vérifie que le parcours existe déjà, elle s'arrete lorsqu'elle trouve un parcours avec le meme nom existant
        $sql_check_parcours = "SELECT COUNT(*) AS count FROM parcours WHERE nom_parcours = :nom_parcours";
        $stmt_check_parcours = $handle->prepare($sql_check_parcours);
        $stmt_check_parcours->bindValue(':nom_parcours', $nom_parcours);
        $result_check_parcours = $stmt_check_parcours->execute()->fetchArray(SQLITE3_ASSOC);

        if ($result_check_parcours['count'] > 0) {
            http_response_code(400);
            echo json_encode(["message" => "Le nom du parcours existe déjà."], JSON_UNESCAPED_UNICODE);
            exit; // Arrêter l'exécution du script si le parcours existe déjà
        }

        // Début de la transaction
        $handle->exec('BEGIN');

        // Insertion du parcours dans la table "parcours"
        $sql_insert_parcours = "INSERT INTO parcours (nom_parcours) VALUES (:nom_parcours)";
        $stmt_insert_parcours = $handle->prepare($sql_insert_parcours);
        $stmt_insert_parcours->bindValue(':nom_parcours', $nom_parcours);
        $stmt_insert_parcours->execute();

        // Récupération de l'ID du parcours inséré
        $id_parcours = $handle->lastInsertRowID();

        // Mise à jour de la colonne "groupe_fk_parcours" dans la table "groupe" pour associé le parcours au groupe selectionner
        $sql_update_groupe = "UPDATE groupe SET groupe_fk_parcours = :parcours_id WHERE nom = :nom_groupe";
        $stmt_update_groupe = $handle->prepare($sql_update_groupe);
        $stmt_update_groupe->bindValue(':parcours_id', $id_parcours);
        $stmt_update_groupe->bindValue(':nom_groupe', $nom_groupe);
        $stmt_update_groupe->execute();

        // Insertion des points du parcours dans la table "coordonnees"
        $sql_insert_points = "INSERT INTO coordonnees (parcours_id, longitude, latitude, nom, note) VALUES (:parcours_id, :longitude, :latitude, :nom, :note)";
        $stmt_insert_points = $handle->prepare($sql_insert_points);

        // Boucle d'insertion des données dans les colonnes de la table 
        foreach ($points as $point) {
            $longitude = $point['coordinates'][0];
            $latitude = $point['coordinates'][1];
            $nom_point = $point['nom'];
            $note_point = $point['note'];

            $stmt_insert_points->bindValue(':parcours_id', $id_parcours);
            $stmt_insert_points->bindValue(':longitude', $longitude);
            $stmt_insert_points->bindValue(':latitude', $latitude);
            $stmt_insert_points->bindValue(':nom', $nom_point);
            $stmt_insert_points->bindValue(':note', $note_point);
            $stmt_insert_points->execute();
        }

        // Fin de la transaction
        $handle->exec('COMMIT');

        http_response_code(201);
        echo json_encode(["message" => "Les données ont été ajoutées avec succès."], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Les données envoyées sont incomplètes."], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "POST"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>