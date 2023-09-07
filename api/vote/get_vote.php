<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Appel de la database et de la table vote
    $handle = new SQLite3("../geoselfing.db");
    $tables = array("vote");

    // Séléctionne avec une requête SELECT 
    foreach ($tables as $table) {

        // Initialise un tableau assiociatif
        $data = [];
        $data['vote'] = [];
        $result = $handle->query("SELECT * FROM vote");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            extract($row);

            $extract_vote = [
                "id" => $id,
                "vote_fk_photo" => $vote_fk_photo,
                "vote_fk_groupe" => $vote_fk_groupe
            ];

            $data['score'][] = $extract_vote;
        }

        // Envoie du code réponse 200-OK
        http_response_code(200);

        // Envoie de la réponse en json
        echo json_encode($data, JSON_PRETTY_PRINT);
    }


} else {
    // Si la méthode n'est pas "GET"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}

?>