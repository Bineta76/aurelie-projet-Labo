<?php
include 'includes/header.php';
session_start();

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}

// Suppression d’un compte rendu
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $stmt = $pdo->prepare("DELETE FROM bilan WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}

// Enregistrement d’un nouveau compte rendu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['texte'])) {
    $texte = trim($_POST['texte']);
    if ($texte !== '') {
        $stmt = $pdo->prepare("INSERT INTO bilan (texte) VALUES (?)");
        $success = $stmt->execute([$texte]);
        if ($success) {
            // Redirection après insertion
            header("Location: index.php");
            exit; // Toujours mettre exit après header Location
        } else {
            echo "Erreur lors de l'insertion.";
        }
    } else {
        echo "Le texte est vide.";
    }
}

// Récupération des comptes rendus pour affichage
$stmt = $pdo->query("SELECT * FROM bilan ORDER BY id DESC");
$bilanListe = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte Rendu Médical</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1 class="mb-4">🩺 Gestion des comptes rendus</h1>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header">Ajouter un nouveau compte rendu</div>
        <div class="card-body">
            <form method="post" action="">
                <div class="mb-3">
                    <textarea name="texte" class="form-control" rows="4" placeholder="Écris ton compte rendu ici..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- Liste des comptes rendus -->
    <?php foreach ($bilanListe as $row): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">📝 Compte rendu #<?= htmlspecialchars($row['id']) ?></h5>
                <p class="card-text"><?= nl2br(htmlspecialchars($row['texte'])) ?></p>
                <a href="?supprimer=<?= urlencode($row['id']) ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Supprimer ce compte rendu ?');">
                   Supprimer
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
