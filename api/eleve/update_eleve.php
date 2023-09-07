<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST"); // Correction : La méthode doit être "POST" pour une mise à jour
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Récupération des données envoyées en JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Vérification si les données nécessaires sont présentes
    if (isset($data['identifiant'])) {

        // Appel de la database et de la table eleve
        $handle = new SQLite3("../geoselfing.db");

        // Préparation de la requête SQL avec des paramètres
        $statement = $handle->prepare("UPDATE eleve SET autorisation = 2 WHERE identifiant = :identifiant");
        $statement->bindValue(':identifiant', $data['identifiant'], SQLITE3_TEXT);

        // Exécution de la requête SQL
        $result = $statement->execute();

        if ($result) {
            // Envoie du code réponse 200-OK
            http_response_code(200);

            // Envoie de la réponse en json
            echo json_encode(["message" => "La mise à jour a été effectuée avec succès."], JSON_PRETTY_PRINT);
        } else {
            // Envoie du code réponse 500-Internal Server Error
            http_response_code(500);
            echo json_encode(["message" => "Une erreur est survenue lors de la mise à jour."], JSON_PRETTY_PRINT);
        }
    } else {
        // Si les données nécessaires ne sont pas présentes
        http_response_code(400);
        echo json_encode(["message" => "Données incomplètes. Veuillez fournir l'identifiant et l'autorisation."], JSON_PRETTY_PRINT);
    }

} else {
    // Si la méthode n'est pas "POST"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>
