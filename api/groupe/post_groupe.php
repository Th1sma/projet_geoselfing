<?php
/**
 * @author Mathis NIVEAU 
 * @date   27/05/2023
 * @description Ce fichier est utilisé par l'API pour la fonctionnalité "GROUPE"
 * @version 1.0
 * ------------------------------------------------------------------------------------------------------
 * @notes Le fichier permet d'ajouter un groupe dans la DB de l'API
 */

// Headers requis
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérification que la méthode utilisée est correcte
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $handle = new SQLite3("../geoselfing.db");
    $table = 'groupe';

    $donnees = json_decode(file_get_contents("php://input"), true);

    // Vérification des données recues, Récupération des données
    if (!empty($donnees['nom']) && !empty($donnees['pin']) && !empty($donnees['avatar'])) {
        $nom = $donnees['nom'];
        $pin = ($donnees['pin']);
        $avatar_base64 = ($donnees['avatar']);

        // Vérification si le nom du groupe existe déjà
        // Vérifie que le groupe existe déjà, elle s'arrête lorsqu'elle trouve un groupe avec le même nom existant
        $sql_check = "SELECT COUNT(*) AS count FROM $table WHERE nom = :nom";
        $stmt_check = $handle->prepare($sql_check);
        $stmt_check->bindValue(':nom', $nom);
        $result = $stmt_check->execute()->fetchArray(SQLITE3_ASSOC);

        if ($result['count'] > 0) {
            http_response_code(400);
            echo json_encode(["message" => "Le nom du groupe existe déjà."], JSON_UNESCAPED_UNICODE);
            exit; // Arrêter l'exécution du script si le groupe existe déjà
        }

        // Préparation de la requête SQL pour l'insertion des données
        $sql = "INSERT INTO $table (nom, pin, avatar, groupe_fk_parcours, groupe_verif_questionnaire) 
                VALUES (:nom, :pin, :avatar, :groupe_fk_parcours, :groupe_verif_questionnaire)";
        $stmt = $handle->prepare($sql);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':pin', $pin);

        // Création du dossier pour les photos prisent depuis l'application
        $chemin_dossier_photos = "../groupes_img/";
        mkdir($chemin_dossier_photos . $nom, 0777, true);

        // Chemin du dossier pour les avatars
        $chemin_dossier_avatar = "../groupes_avatar/";

        // Nom de l'image
        $nom_image = $nom . '_avatar.png';
        // Chemin complet de l'image
        $chemin_image = $chemin_dossier_avatar . $nom_image;
        // Conversion de la base64 en image et sauvegarde
        $avatar_data = base64_decode($avatar_base64);
        if (file_put_contents($chemin_image, $avatar_data)) {

            // Stocker le lien de l'image dans le champ "avatar"
            $stmt->bindValue(':avatar', "http://192.168.0.10/api/groupes_avatar/" . $nom_image);
            $stmt->bindValue(':groupe_fk_parcours', 0);
            $stmt->bindValue(':groupe_verif_questionnaire', 0);

            // Exécution de la requête SQL
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(["message" => "Les données ont été ajoutées avec succès."], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Erreur lors de l'ajout des données."], JSON_UNESCAPED_UNICODE);
            }
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erreur lors de l'enregistrement de l'image."], JSON_UNESCAPED_UNICODE);
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
?>
