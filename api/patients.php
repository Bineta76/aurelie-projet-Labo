<?php
require 'config.php';
require 'db.php';

// Récupération de l'ID si fourni dans l'URL
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            $stmt = $pdo->query("SELECT * FROM patients");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("INSERT INTO patients (nom, prenom, date_naissance) VALUES (?, ?, ?)");
        $stmt->execute([$data['nom'], $data['prenom'], $data['date_naissance']]);
        echo json_encode(['message' => 'Patient ajouté avec succès']);
        break;

    case 'PUT':
        if (!$id) {
            echo json_encode(['error' => 'ID requis']);
            exit;
        }
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("UPDATE patients SET nom=?, prenom=?, date_naissance=? WHERE id=?");
        $stmt->execute([$data['nom'], $data['prenom'], $data['date_naissance'], $id]);
        echo json_encode(['message' => 'Patient modifié avec succès']);
        break;

    case 'DELETE':
        if (!$id) {
            echo json_encode(['error' => 'ID requis']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['message' => 'Patient supprimé avec succès']);
        break;

    default:
        echo json_encode(['error' => 'Méthode non autorisée']);
        break;
}
