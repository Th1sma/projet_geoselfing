<?php
// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Appel de la database et de la table score
    $handle = new SQLite3("../geoselfing.db");
    $table = "score";

    // Vérification si la table n'est pas vide
    $result = $handle->query("SELECT COUNT(*) as count FROM $table");
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $count = $row['count'];

    if ($count > 0) {
        // Table non vide, retourner le code 201
        http_response_code(201);
        echo json_encode(["message" => "La table n'est pas vide !"], JSON_UNESCAPED_UNICODE);
    } else {
        // Table vide, retourner le code 200
        http_response_code(200);
        echo json_encode(["message" => "La table est vide !"], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "GET"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
