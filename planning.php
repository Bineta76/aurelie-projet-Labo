<?php
include 'includes/header.php';
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ajouter un créneau (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO plannings (medecin_id, date, heure_debut, heure_fin, statut) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['medecin_id'],
        $_POST['date'],
        $_POST['heure_debut'],
        $_POST['heure_fin'],
        $_POST['statut']
    ]);

    echo "<script>alert('Créneau ajouté avec succès.'); window.location.href = 'planning.php';</script>";
    exit;
}

// Charger les événements (GET AJAX)
if (isset($_GET['events'])) {
    header('Content-Type: application/json');
    $stmt = $pdo->query("SELECT p.*, m.nom FROM plannings p JOIN medecins m ON p.medecin_id = m.id");
    $events = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $events[] = [
            'title' => $row['nom'] . ' (' . $row['statut'] . ')',
            'start' => $row['date'] . 'T' . $row['heure_debut'],
            'end'   => $row['date'] . 'T' . $row['heure_fin'],
            'color' => $row['statut'] === 'disponible' ? 'green' : 'red'
        ];
    }

    echo json_encode($events);
    exit;
}
?>
