<?php
$host = "localhost";  // Serveur MySQL (par défaut localhost sous XAMPP)
$user = "root";       // Utilisateur MySQL par défaut sous XAMPP
$password = "";           // Aucun mot de passe par défaut sous XAMPP
$dbname = "bdd";        // Base de données existante (ou mets la tienne)

// Connexion MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8",
    $user,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

echo "Connexion réussie à MySQL via MySQLi et PDO ! 🎉";
