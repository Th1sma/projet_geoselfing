<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "GROUPE"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet d'afficher tout les groupes enregistrés dans l'API
 */

// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Appel de la base de données
$handle = new SQLite3("../geoselfing.db");
$table_groupe = 'groupe';

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Vérification si groupe_fk_parcours est spécifié
    if (isset($_GET['groupe_fk_parcours'])) {
        $groupe_fk_parcours = $_GET['groupe_fk_parcours'];
        $result = $handle->query("SELECT * FROM $table_groupe WHERE groupe_fk_parcours = $groupe_fk_parcours");
    } else {
        $result = $handle->query("SELECT * FROM $table_groupe");
    }

    // Vérification du résultat
    if ($result !== false) {
        $groupes = array();

        // Récupération des données dans un tableau
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $groupe_id = $row['id'];

            // Ajouter le groupe au tableau
            $groupes[] = array(
                'id' => $groupe_id,
                'nom' => $row['nom'],
                'pin' => $row['pin'],
                'avatar' => $row['avatar'],
                'groupe_fk_parcours' => $row['groupe_fk_parcours'],
                'groupe_verif_questionnaire' => $row['groupe_verif_questionnaire']
            );
        }

        // Construction du tableau final
        $data = array(
            'groupe' => $groupes
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
