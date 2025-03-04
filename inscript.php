<?php
session_start();
include 'includes/db.php'; // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $login = htmlspecialchars(trim($_POST['login']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $numerodesecuritesociale = htmlspecialchars(trim($_POST['numerodesecuritesociale']));
    $mdp = password_hash($_POST['mdp'], PASSWORD_BCRYPT); // Hachage du mot de passe

 

    // Vérification des doublons
    $stmt = $pdo->prepare("SELECT * FROM patient WHERE email = :email OR numerodesecuritesociale = :nss");
    $stmt->execute([':email' => $email, ':nss' => $numerodesecuritesociale]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['message_error'] = "Cet email ou numéro de sécurité sociale est déjà utilisé.";
        header("Location: inscription.php");
        exit();
    }

    // Insertion des données
    $stmt = $pdo->prepare("INSERT INTO patient (nom, prenom, login, email, numerodesecuritesociale, mdp) VALUES (:nom, :prenom, :login, :email, :nss, :mdp)");
    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':login' => $login,
        ':email' => $email,
        ':nss' => $numerodesecuritesociale,
        ':mdp' => $mdp,
    ]);

    $_SESSION['message_success'] = "Inscription réussie !";
    header("Location: index.php");
    exit();
}
