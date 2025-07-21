<?php
header('Content-Type: application/json');

// Autoriser uniquement les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Méthode non autorisée
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Lire les données JSON du corps de la requête
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier que les champs requis sont présents
if (!isset($data['nom_patient'], $data['nom_docteur'], $data['date_rdv'], $data['heure_rdv'])) {
    http_response_code(400); // Mauvaise requête
    echo json_encode(['error' => 'Données incomplètes']);
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

// Insertion dans la base
try {
    $stmt = $pdo->prepare("
        INSERT INTO rendez_vous (nom_patient, nom_docteur, date_rdv, heure_rdv)
        VALUES (:nom_patient, :nom_docteur, :date_rdv, :heure_rdv)
    ");

    $stmt->execute([
        ':nom_patient' => $data['nom_patient'],
        ':nom_docteur' => $data['nom_docteur'],
        ':date_rdv' => $data['date_rdv'],
        ':heure_rdv' => $data['heure_rdv']
    ]);

    echo json_encode(['success' => true, 'message' => 'Rendez-vous ajouté avec succès']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l\'insertion du rendez-vous']);
}
