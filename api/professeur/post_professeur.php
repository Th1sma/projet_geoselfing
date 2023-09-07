<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Appel de la database et de la table professeur
    $handle = new SQLite3("../geoselfing.db");
    $table = "professeur";

    // Extraction des données envoyées
    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données reçues, Récupération des données
    if (!empty($donnees['identifiant']) && !empty($donnees['mot_de_passe'])) {
        $identifiant = $donnees['identifiant'];
        $mot_de_passe = $donnees['mot_de_passe'];

        // Préparation de la requête SQL
        $sql = "INSERT INTO $table (identifiant, mot_de_passe) VALUES (:identifiant, :mot_de_passe)";
        $stmt = $handle->prepare($sql);
        $stmt->bindValue(':identifiant', $identifiant);
        $stmt->bindValue(':mot_de_passe', $mot_de_passe);

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
