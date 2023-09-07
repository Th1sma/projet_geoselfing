<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Appel de la database et de la table score
    $handle = new SQLite3("../geoselfing.db");
    $table = "score";

    // Extraction des données envoyées
    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données reçues et récupération des données
    if (!empty($donnees)) {
        // Parcourir les données envoyées
        foreach ($donnees as $scoreData) {
            if (!empty($scoreData['scores']) && !empty($scoreData['score_fk_groupe'])) {
                $scores = intval($scoreData['scores']);
                $score_fk_groupe = intval($scoreData['score_fk_groupe']);

                // Préparation de la requête SQL
                $insertQuery = "INSERT INTO $table (scores, score_fk_groupe) VALUES (:scores, :score_fk_groupe)";
                $stmt = $handle->prepare($insertQuery);
                $stmt->bindValue(':scores', $scores);
                $stmt->bindValue(':score_fk_groupe', $score_fk_groupe);

                // Exécution de la requête SQL
                $stmt->execute();
            }
        }

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
