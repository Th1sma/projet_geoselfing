<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Démarrage de la session
session_start();

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Appel de la database et de la table eleve
    $handle = new SQLite3("../geoselfing.db");
    $table = "eleve";

    // Réception des données
    $donneesbrut = file_get_contents("php://input");
    $donnees = json_decode($donneesbrut, true);

    // Vérification des données reçues, Récupération des données
    if (!empty($donnees['identifiant']) && !empty($donnees['autorisation'])) {
        $identifiant = $donnees['identifiant'];
        $autorisation = intval($donnees['autorisation']);

        // Requête SQL pour récupérer les données
        $sql = "SELECT identifiant FROM eleve WHERE identifiant = :identifiant AND autorisation = :autorisation";
        $query = $handle->prepare($sql);
        $query->bindValue(':identifiant', $identifiant);
        $query->bindValue(':autorisation', $autorisation);
        $result = $query->execute()->fetchArray(SQLITE3_ASSOC);

        if (!empty($result)) {

            http_response_code(201);
            echo json_encode(["message" => "Les données ont été trouvés."], JSON_UNESCAPED_UNICODE);

            // Sauvegarde des données de session
            $_SESSION['autorisation'] = $autorisation;
            $_SESSION['identifiant'] = $identifiant;

            exit();

        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erreur : les données n'existent pas."], JSON_UNESCAPED_UNICODE);
        }

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