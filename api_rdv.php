<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");

require "db.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        // Lire tous les rendez-vous
        $stmt = $pdo->query("SELECT * FROM rendez_vous ORDER BY date_rdv, heure_rdv");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case "POST":
        // Ajouter un rendez-vous
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['patient'], $data['docteur'], $data['date_rdv'], $data['heure_rdv'])) {
            echo json_encode(["error" => "Données manquantes"]);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO rendez_vous (patient, docteur, date_rdv, heure_rdv) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['patient'], $data['docteur'], $data['date_rdv'], $data['heure_rdv']]);

        echo json_encode(["success" => "Rendez-vous ajouté"]);
        break;

    case "DELETE":
        // Supprimer un rendez-vous avec l'id passé en paramètre ?id=1
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "ID manquant"]);
            exit;
        }
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("DELETE FROM rendez_vous WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(["success" => "Rendez-vous supprimé"]);
        break;

    default:
        echo json_encode(["error" => "Méthode non supportée"]);
        break;
}
?>
