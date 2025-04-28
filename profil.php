<?php
include 'includes/header.php';
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>
</head>
<body>
    <form method="POST" action="">
        <label>Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
        <br>
        <label>Prénom :</label>
        <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
        <br>
        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <br>
        <label>Numéro de sécurité sociale :</label>
        <input type="text" name="numerodesecuritesociale" value="<?= htmlspecialchars($user['numerodesecuritesociale']) ?>" required pattern="\d{15}">
        <br>
        <label>Mot de passe (laisser vide si inchangé) :</label>
        <input type="password" name="mdp">
        <br>
        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>