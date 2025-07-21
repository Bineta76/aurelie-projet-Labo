<?php
header('Content-Type: application/json');

// Connexion à la base de données
$host = 'localhost';
$db   = 'labo';
$user = 'root';
$pass = ''; // mot de passe vide sous WAMP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->query("SELECT id, titre, start, end FROM rendez_vous");
    $rendezvous = $stmt->fetchAll();
    echo json_encode($rendezvous);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500);
}
?>
