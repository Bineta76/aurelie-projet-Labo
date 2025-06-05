<?php
include 'includes/header.php';
session_start();



// Configuration de la base de données
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
    exit("Erreur de connexion à la base de données : " . $e->getMessage());
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des champs
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mdp'] ?? '';
    $numero = preg_replace('/\D/', '', $_POST['numero_de_securite_sociale'] ?? '');

    // Vérifications
    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp) || empty($numero)) {
        $message = "❌ Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Adresse email invalide.";
    } elseif (strlen($numero) !== 15) {
        $message = "❌ Numéro invalide : il doit contenir exactement 15 chiffres.";
    } else {
        $base = substr($numero, 0, 13);
        $cle = substr($numero, 13, 2);

        if (!function_exists('bcmod')) {
            $message = "❌ Erreur : l'extension BCMath n'est pas activée sur le serveur.";
        } else {
            $cleAttendue = sprintf('%02d', 97 - bcmod($base, '97'));

            if ($cle !== $cleAttendue) {
                $message = "❌ Numéro invalide : clé de contrôle incorrecte.";
            } else {
                try {
                    // Vérifie si l'email est déjà utilisé
                    $verif = $pdo->prepare("SELECT id FROM patient WHERE email = ?");
                    $verif->execute([$email]);
                    if ($verif->fetch()) {
                        $message = "❌ Un compte avec cet email existe déjà.";
                    } else {
                        // Hachage et insertion
                        $hash = password_hash($mdp, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO patient (nom, prenom, email, numero_de_securite_sociale, mdp) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$nom, $prenom, $email, $numero, $hash]);
                        $message = "✅ Inscription réussie. <a href='login.php'>Se connecter</a>";
                    }
                } catch (PDOException $e) {
                    $message = "❌ Erreur lors de l'inscription : " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!-- Formulaire HTML -->
<form method="POST">
    <input type="text" name="nom" placeholder="Nom" required><br>
    <input type="text" name="prenom" placeholder="Prénom" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="numero_de_securite_sociale"
           placeholder="Numéro de sécurité sociale"
           pattern="\d{15}"
           title="15 chiffres sans espaces"
           required><br>
    <input type="password" name="mdp" placeholder="Mot de passe" required><br>
    <button type="submit">S’inscrire</button>
</form>

<?php if (!empty($message)): ?>
    <p style="margin-top: 10px; font-weight: bold;"><?= $message ?></p>
<?php endif; ?>
