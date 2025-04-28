<?php
$pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $date = $_POST['date'] ?? '';
    $heure = $_POST['heure'] ?? '';
    $nom = $_POST['nom'] ?? '';

    if (!empty($titre) && !empty($date) && !empty($heure) && !empty($nom)) {
        $stmt = $pdo->prepare("INSERT INTO rdv (titre, date, heure, nom) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titre, $date, $heure, $nom]);

        // Redirection vers le calendrier après ajout
        header("Location: planning.php"); // à adapter si le fichier s'appelle autrement
        exit;
    } else {
        $message = "Tous les champs doivent être remplis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container my-5">
    <h2>Ajouter un rendez-vous</h2>

    <?php if (!empty($message)) : ?>
        <div class="alert alert-warning"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-3">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>
        <div class="col-md-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="col-md-3">
            <label for="heure" class="form-label">Heure</label>
            <input type="time" class="form-control" id="heure" name="heure" required>
        </div>
        <div class="col-md-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="planning.php" class="btn btn-secondary">Retour</a>
        </div>
    </form>
</body>
</html>
