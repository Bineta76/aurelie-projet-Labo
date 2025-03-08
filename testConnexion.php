<?php

session_start(); // Démarre la session

// Vérifie si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Informations de connexion à la base de données
    $servername = "localhost";   // Serveur de base de données (localhost pour développement local)
    $username = "root";          // Nom d'utilisateur de la base de données
    $password = "";              // Mot de passe de la base de données (vide si aucun mot de passe)
    $dbname = "labo";             // Nom de la base de données

    // Récupère les informations du formulaire
    $user_input = $_POST['username'];   // Nom d'utilisateur soumis
    $user_pass = $_POST['password'];    // Mot de passe soumis

    // Création de la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérification de la connexion
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Protection contre les injections SQL
    $user_input = $conn->real_escape_string($user_input);

    // Requête pour vérifier si l'utilisateur existe dans la base de données
    $sql = "SELECT * FROM patient WHERE username = ?"; // Requête préparée
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_input); // Bind de l'input utilisateur (pour éviter les injections)
    $stmt->execute(); // Exécution de la requête
    $result = $stmt->get_result();

    // Vérification si l'utilisateur existe
    if ($result->num_rows > 0) {
        // L'utilisateur existe, récupérer les informations de l'utilisateur
        $user = $result->fetch_assoc();

        // Vérification du mot de passe (si le mot de passe est haché)
        if (password_verify($user_pass, $user['password'])) {
            // Authentification réussie, création de la session
            $_SESSION['id_patient'] = $user['id_patient']; // ID de l'utilisateur dans la session
            $_SESSION['username'] = $user['username'];     // Nom d'utilisateur dans la session

            // Redirection vers la page d'accueil ou la page protégée
            header("Location: index.php");
            exit;
        } else {
            // Mot de passe incorrect
            $error_message = "Mot de passe incorrect.";
        }
    } else {
        // Nom d'utilisateur incorrect
        $error_message = "Nom d'utilisateur incorrect.";
    }

    // Fermer la requête préparée
    $stmt->close();

    // Fermer la connexion à la base de données
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter</title>
    <!-- Inclure Bootstrap ou ton propre CSS -->
</head>
<body>
    <div class="container w-50">
        <h2 class="text-center">Se connecter</h2>

        <!-- Affichage des erreurs s'il y en a -->
        
        <!-- Formulaire de connexion -->
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur :</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </div>
        </form>
    </div>
</body>
</html>
