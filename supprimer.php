<?php
// supprimer.php

if (!isset($_GET['id'])) {
    die("ID de rendez-vous manquant.");
}

$pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", "");
$id = intval($_GET['id']);

$stmt = $pdo->prepare("DELETE FROM rdv WHERE id = ?");
$stmt->execute([$id]);

header("Location: planning.php"); // Redirige vers la page du calendrier (à adapter si besoin)
exit;
?>
