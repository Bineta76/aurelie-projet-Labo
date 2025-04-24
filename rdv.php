<?php
session_start();
include 'includes/header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Génération d'un token CSRF si inexistant
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Connexion à la base de données
$host = "localhost";
$user = "root";
$password = "";
$dbname = "labo";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    exit("Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()));
}

// Fonction de vérification d'existence d'entité
function verifierEntite(PDO $pdo, string $table, $id): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `$table` WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() > 0;
}

// Initialisation
$errors = [];
$successMessage = '';
$lastDateFormatted = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Échec de la vérification CSRF.";
    }

    $dateInput = $_POST['date'] ?? '';
    $idMedecin = $_POST['medecin'] ?? '';
    $idExamen = $_POST['type_examen'] ?? '';
    $idCabinetMedical = $_POST['cabinet_medical'] ?? '';

    if (empty($dateInput) || empty($idMedecin) || empty($idExamen) || empty($idCabinetMedical)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $dateInput);
    if (!$dateTime || $dateInput !== $dateTime->format('Y-m-d\TH:i')) {
        $errors[] = "Format de date invalide.";
    } else {
        $dateFormatted = $dateTime->format('Y-m-d H:i:s'); // format BDD
        $lastDateFormatted = $dateTime->format('d/m/Y à H:i'); // affichage humain
    }

    if (empty($errors)) {
        if (!verifierEntite($pdo, 'cabinet_medical', $idCabinetMedical)) {
            $errors[] = "Cabinet médical invalide.";
        }

        if (!verifierEntite($pdo, 'medecin', $idMedecin)) {
            $errors[] = "Médecin invalide.";
        }

        if (!verifierEntite($pdo, 'examen', $idExamen)) {
            $errors[] = "Type d'examen invalide.";
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO rdv (`date`, id_medecin, id_examen, id_cabinet_medical) VALUES (?, ?, ?, ?)");
                $stmt->execute([$dateFormatted, $idMedecin, $idExamen, $idCabinetMedical]);
                
                // ✅ Affichage confirmation avec heure

            } catch (PDOException $e) {
                $errors[] = "Erreur lors de l'enregistrement : " . htmlspecialchars($e->getMessage());
            }
        }
    }
}

// Récupération des données
$cabinetsMedical = $pdo->query("SELECT id, Nom FROM cabinet_medical")->fetchAll();
$medecins = $pdo->query("SELECT id, Nom, Prenom FROM medecin")->fetchAll();
$examens = $pdo->query("SELECT id, nom FROM examen")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prise de rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Prendre un rendez-vous</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif (!empty($successMessage)): ?>
        <div class="alert alert-success">
            <?= $successMessage ?>
        </div>
    <?php endif; ?>

    <form method="post" class="bg-white p-4 rounded shadow-sm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="mb-3">
            <label for="date" class="form-label">Date et heure</label>
            <input type="datetime-local" class="form-control" name="date" id="date" required>
        </div>

        <div class="mb-3">
            <label for="medecin" class="form-label">Médecin</label>
            <select class="form-select" name="medecin" id="medecin" required>
                <option value="">-- Choisir un médecin --</option>
                <?php foreach ($medecins as $medecin): ?>
                    <option value="<?= $medecin['id'] ?>">
                        <?= htmlspecialchars($medecin['Prenom'] . ' ' . $medecin['Nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="type_examen" class="form-label">Type d'examen</label>
            <select class="form-select" name="type_examen" id="type_examen" required>
                <option value="">-- Choisir un examen --</option>
                <?php foreach ($examens as $examen): ?>
                    <option value="<?= $examen['id'] ?>">
                        <?= htmlspecialchars($examen['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label for="cabinet_medical" class="form-label">Cabinet médical</label>
            <select class="form-select" name="cabinet_medical" id="cabinet_medical" required>
                <option value="">-- Choisir un cabinet --</option>
                <?php foreach ($cabinetsMedical as $cabinet): ?>
                    <option value="<?= $cabinet['id'] ?>">
                        <?= htmlspecialchars($cabinet['Nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
