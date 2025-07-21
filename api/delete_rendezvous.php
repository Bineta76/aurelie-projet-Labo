<?php
header('Content-Type: application/json');

// Autoriser uniquement les requêtes DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Lire les données JSON envoyées
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier que l'ID du rendez-vous est présent
if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du rendez-vous manquant']);
    exit;
}

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=labo", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    exit;
}

// Suppression du rendez-vous
try {
    $stmt = $pdo->prepare("DELETE FROM rendez_vous WHERE id = :id");
    $stmt->execute([':id' => $data['id']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Rendez-vous supprimé avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucun rendez-vous trouvé avec cet ID']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la suppression']);
}
