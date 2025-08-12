<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
try {
    $pdo = new PDO('mysql:host=localhost;dbname=labo;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base : ' . $e->getMessage());
}

// Déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

$message = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';

    if ($email === '' || $password === '') {
        $message = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Adresse email invalide.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nom, mdp FROM patient WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $message = 'Aucun utilisateur trouvé avec cet email.';
        } else {
            if (password_verify($password, $row['mdp'])) {
                session_regenerate_id(true);
                $_SESSION['id_patient'] = $row['id'];
                $_SESSION['utilisateur'] = $row['nom'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(24));

                header('Location: index.php');
                exit;
            } else {
                $message = 'Mot de passe incorrect.';
                // En mode dev, tu peux ajouter du debug dans le log PHP :
                error_log("Mot de passe incorrect pour email : $email");
            }
        }
    }
}
?>
