<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Appel de la database et de la table eleve
    $handle = new SQLite3("../geoselfing.db");
    $table = "photo";

    // Extraction des données envoyées
    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données reçues, Récupération des données
    if (!empty($donnees['img']) && !empty($donnees['photo_fk_groupe'])) {
        $img = $donnees['img'];
        $photo_fk_groupe = intval($donnees['photo_fk_groupe']);

        // Préparation de la requête SQL
        $sql = "INSERT INTO $table (img, photo_fk_groupe) VALUES (:img, :photo_fk_groupe)";
        $stmt = $handle->prepare($sql);
        $stmt->bindValue(':img', $img);
        $stmt->bindValue(':photo_fk_groupe', $photo_fk_groupe);

        // Exécution de la requête SQL
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Les données ont été ajoutées avec succès."], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erreur lors de l'ajout des données."], JSON_UNESCAPED_UNICODE);
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
