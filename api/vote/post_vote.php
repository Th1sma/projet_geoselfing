<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Appel de la database et de la table vote
    $handle = new SQLite3("../geoselfing.db");
    $table = "vote";

    // Réception des données
    $donneesbrut = file_get_contents("php://input");
    $donnees = json_decode($donneesbrut, true);

    // Vérification des données reçues, Récupération des données
    if (!empty($donnees['votes']) && !empty($donnees['vote_fk_groupe'])) {
        $votes = $donnees['votes'];
        $vote_fk_groupe = $donnees['vote_fk_groupe'];

        for ($i = 0; $i < count($votes); $i++) {
            // Préparation de la requête SQL
            $sql = "INSERT INTO $table (votes, vote_fk_groupe) VALUES (:votes, :vote_fk_groupe)";
            $stmt = $handle->prepare($sql);
            $stmt->bindValue(':votes', $votes[$i], SQLITE3_INTEGER);
            $stmt->bindValue(':vote_fk_groupe', $vote_fk_groupe[$i], SQLITE3_INTEGER);

            // Exécution de la requête SQL
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(["message" => "Les données ont été ajoutées avec succès."], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erreur lors de l'ajout des données."], JSON_UNESCAPED_UNICODE);
            }
        }

    } else {
        http_response_code(400);
        echo json_encode(["message" => "Les données envoyées sont incomplètes."], JSON_UNESCAPED_UNICODE);
    }
    $handle->close(); // Fermeture de la connexion à la base de données
    unset($handle);

} else {
    // Si la méthode n'est pas "POST"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>