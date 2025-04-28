ù<?php
include 'includes/header.php';
session_start();


// Connexion à la base de données
$host = 'localhost';
$db = 'labo';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si l'utilisateur est bien connecté
if (!isset($_SESSION['user_id'])) {
    die("Accès interdit : utilisateur non identifié.");
}

$user_id = $_SESSION['user_id'];

// Récupérer les données actuelles de l'utilisateur
$sql = "SELECT nom, prenom, email, numerodesecuritesociale FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécurisation des entrées
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $nss = trim($_POST['numerodesecuritesociale']);
    $mdp = $_POST['mdp'];

    // Vérifier la validité des données
    if (!$email) {
        die("Adresse e-mail invalide.");
    }

    if (!preg_match('/^[0-9]{15}$/', $nss)) {
        die("Numéro de sécurité sociale invalide.");
    }

    // Hachage du mot de passe si modifié
    $mdp_hash = !empty($mdp) ? password_hash($mdp, PASSWORD_DEFAULT) : $user['mdp'];

    // Mettre à jour les données de l'utilisateur
    $sql = "UPDATE users SET nom = ?, prenom = ?, email = ?, numerodesecuritesociale = ?, mdp = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$nom, $prenom, $email, $nss, $mdp_hash, $user_id])) {
        echo "<div class='alert alert-success'>Profil mis à jour avec succès !</div>";
        // Rafraîchir les données après mise à jour
        $user = ['nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'numerodesecuritesociale' => $nss];
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la mise à jour.</div>";
    }
}
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