<?php
session_start();
include 'includes/header.php';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=labo;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

$message = '';
$mode = 'connexion'; // par défaut

if (isset($_GET['action']) && $_GET['action'] === 'inscription') {
    $mode = 'inscription';
}

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ------------------- INSCRIPTION -------------------
    if (isset($_POST['inscription'])) {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mdp = $_POST['mot_de_passe'] ?? '';

        if ($nom === '' || $email === '' || $mdp === '') {
            $message = "Tous les champs sont obligatoires.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Email invalide.";
        } else {
            // Vérifier si email existe
            $stmt = $pdo->prepare("SELECT id FROM patient WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $message = "Cet email est déjà enregistré.";
            } else {
                $hash = password_hash($mdp, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO patient (nom, email, mot_de_passe) VALUES (?, ?, ?)");
                if ($stmt->execute([$nom, $email, $hash])) {
                    $message = "Inscription réussie. Vous pouvez maintenant vous connecter.";
                    $mode = 'connexion';
                } else {
                    $message = "Erreur lors de l'inscription.";
                }
            }
        }
    }

    // ------------------- CONNEXION -------------------
    elseif (isset($_POST['connexion'])) {
        $email = trim($_POST['email'] ?? '');
        $mdp = $_POST['mot_de_passe'] ?? '';

        if ($email === '' || $mdp === '') {
            $message = "Tous les champs sont obligatoires.";
        } else {
            $stmt = $pdo->prepare("SELECT id, nom, mot_de_passe FROM patient WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
    // Debug : affiche le mot de passe saisi et le hash en base
    var_dump($mdp, $user['mot_de_passe']);
    echo '<br>';

    if (password_verify($mdp, $user['mot_de_passe'])) {
        session_regenerate_id(true);
        $_SESSION['id_patient'] = $user['id'];
        $_SESSION['utilisateur'] = $user['nom'];
        header("Location: index.php");
        exit;
    } else {
        $message = "Mot de passe incorrect.";
    }

                $message = "Email inconnu.";
            }
        }
    }
}
?>
<?php
header('Content-Type: application/json');

// Connexion MySQL (modifie si ton mot de passe MySQL n’est pas vide)
$conn = new mysqli("localhost", "root", "", "labo");

if ($conn->connect_error) {
    die(json_encode(["error" => "Erreur connexion DB"]));
}

$sql = "SELECT id, nom_examen FROM examens";
$result = $conn->query($sql);

$examens = [];
while ($row = $result->fetch_assoc()) {
    $examens[] = $row;
}

echo json_encode($examens);

$conn->close();
?>

