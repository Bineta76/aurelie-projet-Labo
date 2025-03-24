<?php
session_start();
include 'includes/header.php'; // Chargement du header


// Connexion à la base de données
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
    


// Initialisation des variables
$patient = [];
$errors = [];

// Récupérer les infos du patient
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM dossier_patient WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $patient = $stmt->fetch();

            if (!$patient) {
                $_SESSION['error'] = "Patient introuvable.";
                header("Location: liste_patients.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la récupération des données : " . htmlspecialchars($e->getMessage());
            header("Location: liste_patients.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "ID invalide.";
        header("Location: liste_patients.php");
        exit();
    }
}

// Mettre à jour le patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        $errors[] = "L'ID est invalide ou manquant.";
    }

    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $datedenaissance = DateTime::createFromFormat('d-m-Y', $_POST['datedenaissance'] ?? '');
    $lieudenaissance = trim($_POST['lieudenaissance'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $numerodesecuritesociale = trim($_POST['numerodesecuritesociale'] ?? '');

    // Validation des champs
    if (empty($nom) || strlen($nom) > 50) {
        $errors[] = "Le nom doit contenir entre 1 et 50 caractères.";
    }

    if (empty($prenom) || strlen($prenom) > 50) {
        $errors[] = "Le prénom doit contenir entre 1 et 50 caractères.";
    }

    if (!$datedenaissance || $datedenaissance->format('d-m-Y') !== $_POST['datedenaissance']) {
        $errors[] = "Date de naissance invalide.";
    }

    if (empty($lieudenaissance) || strlen($lieudenaissance) > 100) {
        $errors[] = "Le lieu de naissance est invalide (max 100 caractères).";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide.";
    }

    if (!preg_match('/^\d{15}$/', $numerodesecuritesociale)) {
        $errors[] = "Le numéro de sécurité sociale doit contenir exactement 15 chiffres.";
    }

    // Si aucune erreur, exécuter la mise à jour
    if (empty($errors)) {
        try {
            // Vérification de l'ID avant d'exécuter la requête
            if (!empty($id)) {
                $stmt = $pdo->prepare("UPDATE dossier_patient SET 
                    nom = :nom,
                    prenom = :prenom,
                    datedenaissance = :datedenaissance,
                    lieudenaissance = :lieudenaissance,
                    email = :email,
                    numerodesecuritesociale = :numerodesecuritesociale
                    WHERE id = :id");

                $stmt->execute([
                    'id' => $id,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'datedenaissance' => $datedenaissance->format('d-m-Y'),
                    'lieudenaissance' => $lieudenaissance,
                    'email' => $email,
                    'numerodesecuritesociale' => $numerodesecuritesociale
                ]);

                $_SESSION['success'] = "Patient mis à jour avec succès.";
                header("Location: liste_patients.php");
                exit();
            } else {
                $errors[] = "ID invalide.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la mise à jour : " . htmlspecialchars($e->getMessage());
        }
    }
}

// Affichage des erreurs si présentes
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p class='error'>$error</p>";
    }
}
?>