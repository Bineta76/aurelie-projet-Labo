<?php
include 'includes/header.php';
session_start();


try {
    $pdo = new PDO('mysql:host=localhost;dbname=labo;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php"); // retourne sur la page d'accueil
    exit;
}

$message = '';
$mode = 'connexion'; // par défaut

if (isset($_GET['action']) && $_GET['action'] === 'inscription') {
    $mode = 'inscription';
}

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // INSCRIPTION
    if (isset($_POST['inscription'])) {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mdp = $_POST['mot_de_passe'] ?? '';

        if ($nom === '' || $email === '' || $mdp === '') {
            $message = "❌ Tous les champs sont obligatoires.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "❌ Email invalide.";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM patient WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = "❌ Cet email est déjà enregistré.";
            } else {
                $hash = password_hash($mdp, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO patient (nom, email, mot_de_passe) VALUES (?, ?, ?)");
                if ($stmt->execute([$nom, $email, $hash])) {
                    $message = "✅ Inscription réussie. Vous pouvez maintenant vous connecter.";
                    $mode = 'connexion';
                } else {
                    $message = "❌ Erreur lors de l'inscription.";
                }
            }
        }
    }

    // CONNEXION
    if (isset($_POST['connexion'])) {
        $email = trim($_POST['email'] ?? '');
        $mdp = $_POST['mot_de_passe'] ?? '';

        if ($email === '' || $mdp === '') {
            $message = "❌ Tous les champs sont obligatoires.";
        } else {
            $stmt = $pdo->prepare("SELECT id, nom, mot_de_passe FROM patient WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($mdp, $user['mot_de_passe'])) {
                session_regenerate_id(true);
                $_SESSION['id_patient'] = $user['id'];
                $_SESSION['utilisateur'] = $user['nom'];
                header("Location: index.php"); // retour accueil
                exit;
            } else {
                $message = "❌ Email ou mot de passe incorrect.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo ucfirst($mode); ?> - Laboratoire Médical</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <B><center>Bienvenue sur le site du laboratoire médical
        HEALTH NORTH
    <center></B>

<img src="http://localhost/labo/image labo.jpg" alt="Logo du laboratoire" style="display:block; margin:20px auto; max-width:200px;">


    <?php if (isset($_SESSION['id_patient'])): ?>
        <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['utilisateur']); ?> 👋</h2>
        <p>Vous êtes connecté à votre espace patient.</p>
        <p style="margin-top:20px;">
            <a href="index.php?action=logout" style="padding:10px 20px; background:#c00; color:#fff; border-radius:5px; text-decoration:none;">Se déconnecter</a>
        </p>
    <?php else: ?>

        <h2><?php echo ($mode === 'connexion') ? "Connexion" : "Inscription"; ?></h2>

        <?php if ($message): ?>
            <p style="color:red;"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if ($mode === 'connexion'): ?>
            <form method="post">
                <div>
                    <label>Email :</label>
                    <input type="email" name="email" required>
                </div>
                <div>
                    <label>Mot de passe :</label>
                    <input type="password" name="mot_de_passe" required>
                </div>
                <div style="margin-top:10px;">
                    <button type="submit" name="connexion">Se connecter</button>
                </div>
            </form>
            <p>Pas encore de compte ? <a href="?action=inscription">Inscrivez-vous</a></p>
        <?php else: ?>
            <form method="post">
                <div>
                    <label>Nom :</label>
                    <input type="text" name="nom" required>
                </div>
                <div>
                    <label>Email :</label>
                    <input type="email" name="email" required>
                </div>
                <div>
                    <label>Mot de passe :</label>
                    <input type="password" name="mot_de_passe" required>
                </div>
                <div style="margin-top:10px;">
                    <button type="submit" name="inscription">S'inscrire</button>
                </div>
            </form>
            <p>Déjà inscrit ? <a href="?action=connexion">Connectez-vous</a></p>
        <?php endif; ?>

    <?php endif; ?>

</div>

</body>
</html>
