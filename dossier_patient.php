<?php
session_start();
include 'includes/header.php';

$host = "localhost";            
$dbname = "labo";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . htmlspecialchars($e->getMessage()));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $date_naissance = $_POST["date_naisssance"];
    $numero_secu = $_POST["numero_secu"];
    $mdp = password_hash($_POST["mdp"], PASSWORD_BCRYPT); 

    try {
        $stmt = $pdo->prepare("INSERT INTO patient (nom, prenom, date_naissance, numero_secu, mdp) VALUES (:nom, :prenom, :date_naissance, :numero_secu, :mdp)");
        $stmt->execute([
            ":nom" => $nom,
            ":prenom" => $prenom,
            ":date_naissance" => $date_naissance,
            ":numero_secu" => $numero_secu,
            ":mdp" => $mdp
        ]);
        echo "<p class='success'>Patient ajouté avec succès.</p>";
    } catch (PDOException $e) {
        echo "<p class='error'>Erreur lors de l'ajout du patient : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM patient");
    $stmt->execute();
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<p class='error'>Erreur lors de la récupération des patients : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM dossier_medical");
    $stmt->execute();
    $dossiers_medical = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<p class='error'>Erreur lors de la récupération des dossiers médicaux : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}
?>

<h1>Liste des patients et leurs dossiers médicaux</h1>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Patient</title>
</head>
<body>
<h2>Création d'un dossier patient :</h2>
    <form method="post" class="mt-4">
        <div class="form-group mb-3">
            <label for="nom">nom :</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="prenom">prénom :</label>
            <input type="text" id="prenom" name="prenom" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="date_naissance">date_naissance :</label>
            <input type="date_naissance" id="date_naissance" name="date_naissance" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="numero_secu">numero_secu:</label>
            <textarea id="nnumero_secu" name="numero_secu" class="form-control" rows="3"></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="mdp">mot de passe :</label>
            <input type="password" id="mdp" name="mdp" class="form-control" required>
        </div>
        <input type="submit" value="S'inscrire">
    </form>
</body>
</html>