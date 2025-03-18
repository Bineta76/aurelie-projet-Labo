<?php 
session_start();
include 'includes/header.php';

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
    // Récupération des données
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
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

    $datedenaissance = DateTime::createFromFormat('Y-m-d', $_POST['datedenaissance'] ?? '');
    if (!$datedenaissance || $datedenaissance->format('Y-m-d') !== $_POST['datedenaissance']) {
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
            $stmt = $pdo->prepare("UPDATE dossier_patient SET 
                nom = :nom,
                prenom = :prenom,
                datedenaissance = :datedenaissance,
                lieudenaissance = :lieudenaissance,
                email = :email,
                numerodesecuritesociale = :numerodesecuritesociale
                WHERE id = :id");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'create');
            $stmt->execute([
                'id' => $id,
                'nom' => $nom,
                'prenom' => $prenom,
                'datedenaissance' => $datedenaissance->format('Y-m-d'),
                'lieudenaissance' => $lieudenaissance,
                'email' => $email,
                'numerodesecuritesociale' => $numerodesecuritesociale
            ]);

            $_SESSION['success'] = "Patient mis à jour avec succès.";
            header("Location: liste_patients.php");
            exit();

        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la mise à jour : " . htmlspecialchars($e->getMessage());
        }
    }
} 
require_once 'require.php';
require_once 'includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Modifier un Patient</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <h5>Erreurs :</h5>
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="border p-4 rounded bg-light">
    <input type="hidden" name="id" value="<?php echo isset($patient['id']) ? htmlspecialchars($patient['id'], ENT_QUOTES, 'UTF-8') : ''; ?>">


    

        
        <div class="row g-3">
    <div class="col-md-6">
        <label for="nom" class="form-label">Nom :</label>
        <input type="text" id="nom" name="nom" 
            value="<?= htmlspecialchars($patient['nom'] ?? ''); ?>" 
            class="form-control"
            required 
            maxlength="50">
    </div>

    <div class="col-md-6">
        <label for="prenom" class="form-label">Prénom :</label>
        <input type="text" id="prenom" name="prenom" 
            value="<?= htmlspecialchars($patient['prenom'] ?? ''); ?>" 
            class="form-control"
            required 
            maxlength="50">
    </div>

    <div class="col-md-6">
        <label for="datedenaissance" class="form-label">Date de naissance :</label>
        <input type="date" id="datedenaissance" name="datedenaissance" 
            value="<?= htmlspecialchars($patient['datedenaissance'] ?? date('Y-m-d')); ?>" 
            class="form-control"
            required>
    </div>

    <div class="col-md-6">
        <label for="lieudenaissance" class="form-label">Lieu de naissance :</label>
        <input type="text" id="lieudenaissance" name="lieudenaissance" 
            value="<?= htmlspecialchars($patient['lieudenaissance'] ?? ''); ?>" 
            class="form-control"
            required 
            maxlength="100">
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email :</label>
        <input type="email" id="email" name="email" 
            value="<?= htmlspecialchars($patient['email'] ?? ''); ?>" 
            class="form-control"
            required>
    </div>

    <div class="col-md-6">
        <label for="numerodesecuritesociale" class="form-label">Numéro de sécurité sociale :</label>
        <input type="text" id="numerodesecuritesociale" name="numerodesecuritesociale" 
            value="<?= htmlspecialchars($patient['numerodesecuritesociale'] ?? ''); ?>" 
            class="form-control"
            required 
            maxlength="15"
            pattern="\d{15}"
            title="15 chiffres requis">
    </div>

            <div class="col-12 mt-4">
                <button type="submit" name="modifier" class="btn btn-primary me-2">
                    <i class="bi bi-save"></i> Mettre à jour
                </button>
                <a href="liste_patients.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </form>
</div>

<?php include("includes/footer.php"); ?>
