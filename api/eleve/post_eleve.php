<?php
// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Appel de la database et de la table correspondante
    $handle = new SQLite3("../geoselfing.db");
    $table = "eleve"; 

    // Réception des données
    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données reçues, Récupération des données
    if (is_array($donnees)) {
        foreach ($donnees as $item) {
            if (!empty($item['identifiant']) && !empty($item['autorisation'])) {
                $identifiant = $item['identifiant'];
                $autorisation = $item['autorisation'];
            
                // Préparation de la requête SQL
                $sql = "INSERT INTO $table (identifiant, autorisation) VALUES (:identifiant, :autorisation)";
                $stmt = $handle->prepare($sql);
                $stmt->bindValue(':identifiant', $identifiant, SQLITE3_TEXT);
                $stmt->bindValue(':autorisation', $autorisation, SQLITE3_INTEGER);              

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
                break;
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Les données envoyées sont invalides."], JSON_UNESCAPED_UNICODE);
    }

    $handle->close(); // Fermeture de la connexion à la base de données
    unset($handle);

} else {
    // Si la méthode n'est pas "POST"
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée."], JSON_UNESCAPED_UNICODE);
}
?>