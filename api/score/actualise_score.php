<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

    // Appel de la database et de la table score
    $handle = new SQLite3("../geoselfing.db");
    $table = "score";

    // Préparation de la requête SQL
    $updateQuery = "
    UPDATE $table
    SET scores = (
        SELECT SUM(votes)
        FROM vote
        WHERE vote_fk_groupe = $table.score_fk_groupe
    )
    WHERE score_fk_groupe IN (
        SELECT vote_fk_groupe
        FROM vote
    )";

    // Exécution de la requête SQL
    if ($handle->exec($updateQuery) !== false) {
        http_response_code(200);
        echo json_encode(["message" => "Les scores ont été mis à jour avec succès."], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Erreur lors de la mise à jour des scores."], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "PUT"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}