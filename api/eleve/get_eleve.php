<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Appel de la database et de la table eleve
    $handle = new SQLite3("../geoselfing.db");
    $tables = array("eleve");

    // Sélectionne les identifiants quand l'autorisation = 2 avec une requête SELECT 
    foreach ($tables as $table) {

        // Initialise un tableau assiociatif
        $data = [];
        $data['eleve'] = [];
        $result = $handle->query("SELECT * FROM eleve WHERE autorisation = 1");

        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            extract($row);

            $extract_eleve = [
                "id" => $id,
                "identifiant" => $identifiant,
                "autorisation" => $autorisation
            ];

            $data['eleve'][] = $extract_eleve;
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