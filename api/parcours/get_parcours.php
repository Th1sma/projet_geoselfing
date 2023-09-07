<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "PARCOURS"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet d'afficher tous les parcours de la DB API
 */

// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Appel de la base de données
    $handle = new SQLite3("../geoselfing.db");
    $table_parcours = 'parcours';
    $table_coordonnees = 'coordonnees';

    // Lecture du paramètre de requête nom_parcours
    $nom_parcours = isset($_GET['nom_parcours']) ? $_GET['nom_parcours'] : null;
    $id_parcours = isset($_GET['id_parcours']) ? $_GET['id_parcours'] : null;

    // Préparation de la requête SQL
    $sql = "SELECT p.id_parcours, p.nom_parcours, c.id, c.longitude, c.latitude, c.nom, c.note, c.valide
        FROM $table_parcours p
        INNER JOIN $table_coordonnees c ON p.id_parcours = c.parcours_id";

    // Recherche par nom de parcours
    if ($nom_parcours !== null) {
        $sql .= " AND p.nom_parcours = :nom_parcours";
    }
    // Recherche par id 
    if ($id_parcours !== null) {
        $sql .= " AND p.id_parcours = :id_parcours";
    }

    $stmt = $handle->prepare($sql);

    // Liaison des paramètres de requête à la requête préparée
    if ($nom_parcours !== null) {
        $stmt->bindValue(':nom_parcours', $nom_parcours, SQLITE3_TEXT);
    }

    if ($id_parcours !== null) {
        $stmt->bindValue(':id_parcours', $id_parcours, SQLITE3_INTEGER);
    }

    $result = $stmt->execute();

    // Vérification du résultat
    if ($result !== false) {
        $parcours = array();

        // Récupération des données dans un tableau
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $parcours_id = $row['id_parcours'];
            $point_id = $row['id'];

            // Vérifier si le parcours existe déjà dans le tableau
            if (!isset($parcours[$parcours_id])) {
                // Si le parcours n'existe pas encore dans le tableau, l'ajouter avec ses données
                $parcours[$parcours_id] = array(
                    'id_parcours' => $parcours_id,
                    // Ajout de l'ID du parcours
                    'nom_parcours' => $row['nom_parcours'],
                    'points' => array()
                );
            }

            // Ajouter le point associé au parcours
            $parcours[$parcours_id]['points'][] = array(
                'id' => $point_id,
                'longitude' => $row['longitude'],
                'latitude' => $row['latitude'],
                'nom' => $row['nom'],
                'note' => $row['note'],
                'valide' => $row['valide'],
            );
        }

        // Construction du tableau final
        $data = array(
            'parcours' => array_values($parcours)
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