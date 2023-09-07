<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    
    // Appel de la database et de la table à modifier
    $handle = new SQLite3("../geoselfing.db");
    $table = "professeur";

    // Extraction des données envoyées
    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données reçues, Récupération des données
    if (!empty($donnees['id'])) {
        $id = $donnees['id'];

        // Préparation de la requête SQL
        $sql = "DELETE FROM $table WHERE id = :id";
        $stmt = $handle->prepare($sql);
        $stmt->bindValue(':id', $id);

        // Exécution de la requête SQL
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(["message" => "Les données ont été supprimées avec succès."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erreur lors de la suppression des données."], JSON_UNESCAPED_UNICODE);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "L'identifiant envoyé est incomplet."], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Si la méthode n'est pas "DELETE"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}

?>