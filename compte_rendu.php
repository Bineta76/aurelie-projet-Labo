<?php
// Connexion MySQL
$pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", "");

// Suppression
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM compte_rendu WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: compte_rendu.php");
    exit();
}

// Ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $contenu = $_POST['contenu'] ?? '';
    if (!empty($titre) && !empty($contenu)) {
        $stmt = $pdo->prepare("INSERT INTO compte_rendu (titre, contenu) VALUES (?, ?)");
        $stmt->execute([$titre, $contenu]);
        header("Location: compte_rendu.php");
        exit();
    }
}

// Récupération des données
$stmt = $pdo->query("SELECT * FROM compte_rendu ORDER BY date_creation DESC");
$comptes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte Rendu Médical</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1, h2 { color: #2c3e50; }
        form { margin-bottom: 30px; }
        input[type="text"], textarea {
            width: 100%; padding: 10px; margin: 5px 0 15px;
            border: 1px solid #ccc; border-radius: 4px;
        }
        button {
            background-color: #3498db; color: white;
            padding: 10px 20px; border: none; border-radius: 4px;
        }
        table {
            border-collapse: collapse; width: 100%; margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd; padding: 10px; vertical-align: top;
        }
        th { background-color: #f5f5f5; }
        .btn-delete {
            background-color: #e74c3c; color: white;
            padding: 6px 12px; text-decoration: none;
            border-radius: 4px;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

    <h1>Créer un Compte Rendu</h1>

    <form method="POST">
        <label for="titre">Titre :</label>
        <input type="text" name="titre" id="titre" required>

        <label for="contenu">Contenu :</label>
        <textarea name="contenu" id="contenu" rows="5" required></textarea>

        <button type="submit">Enregistrer</button>
    </form>

    <h2>Liste des Comptes Rendus</h2>

    <?php if (count($comptes) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Contenu</th>
                <th>Action</th>
            </tr>
            <?php foreach ($comptes as $cr): ?>
            <tr>
                <td><?= $cr['id'] ?></td>
                <td><?= htmlspecialchars($cr['titre']) ?></td>
                <td><?= nl2br(htmlspecialchars($cr['contenu'])) ?></td>
                <td>
                    <a class="btn-delete" href="?delete=<?= $cr['id'] ?>" onclick="return confirm('Supprimer ce compte rendu ?');">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Aucun compte rendu disponible.</p>
    <?php endif; ?>

</body>
</html>
