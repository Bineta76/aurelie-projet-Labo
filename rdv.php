<?php
session_start();

include 'includes/header.php';
include 'includes/db.php'; // Connexion centralisée à la base de données

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérification de la session de l'utilisateur
if (!isset($_SESSION['id_patient'])) {
    echo "Erreur : Vous devez être connecté pour prendre un rendez-vous.";
    exit;
}

$id_patient = $_SESSION['id_patient'];

// Génération d'un jeton CSRF si ce n'est pas déjà fait
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    // Requête pour récupérer les données des listes déroulantes
    $stmtExamen = $pdo->query("SELECT * FROM examen");
    $examens = $stmtExamen->fetchAll(PDO::FETCH_ASSOC);

    $stmtCabinetMedical = $pdo->query("SELECT * FROM `cabinet_médical`");
    $cabinetsMedical = $stmtCabinetMedical->fetchAll(PDO::FETCH_ASSOC);

    $stmtMedecin = $pdo->query("SELECT * FROM médecin");
    $medecins = $stmtMedecin->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}

// Traitement du formulaire pour enregistrer le rendez-vous
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $centre = htmlspecialchars($_POST['centre']);
    $medecin = htmlspecialchars($_POST['medecin']);
    $date = htmlspecialchars($_POST['date']);
    $type_examen = htmlspecialchars($_POST['type_examen']);

    if ($centre && $medecin && $date && $type_examen) {
        try {
            // Requête d'insertion du rendez-vous
            $stmt = $pdo->prepare("
                INSERT INTO rdv (id_medecin, id_cabinetMedical, id_examen, id_patient, Date)
                VALUES (:medecin, :centre, :type_examen, :id_patient, :Date)
            ");
            $stmt->bindParam(':medecin', $medecin);
            $stmt->bindParam(':centre', $centre);
            $stmt->bindParam(':type_examen', $type_examen);
            $stmt->bindParam(':id_patient', $id_patient);
            $stmt->bindParam(':Date', $date);

            if ($stmt->execute()) {
                $_SESSION['message_success'] = "Le rendez-vous a été enregistré avec succès.";
                header("Location: index.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Erreur lors de l'enregistrement du rendez-vous.</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Veuillez remplir tous les champs requis.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre un rendez-vous</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
</head>

<body>
    <div class="container mt-5">
        <div class="topnav">
            <a href="index.php" aria-label="Accueil">Accueil</a>
            <a href="quiSommesNous.php" aria-label="Qui sommes-nous ?">Qui sommes-nous?</a>
            <a href="carte.php" aria-label="Nous contacter">Contact</a>
            <a class="active" href="lien.php" aria-label="Créer un rendez-vous">Créer un rendez-vous</a>
            <a href="planningmedecin.php" aria-label="Voir le planning du médecin">Planning</a>
            <a href="centre.php" aria-label="Voir la liste des centres disponibles">Liste des centres</a>
        </div>

        <h1 class="text-center">Prise de rendez-vous médical</h1>

        <!-- Formulaire de prise de rendez-vous -->
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <!-- Sélection du cabinet médical -->
            <div class="form-group">
                <label for="centre">Cabinet Médical</label>
                <select id="centre" name="centre" class="form-control" required>
                    <option value="">Choisissez un cabinet médical</option>
                    <?php foreach ($cabinetsMedical as $cabinet): ?>
                        <option value="<?= htmlspecialchars($cabinet['id']) ?>">
                            <?= htmlspecialchars($cabinet['Nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Sélection du médecin -->
            <div class="form-group">
                <label for="medecin">Médecin</label>
                <select id="medecin" name="medecin" class="form-control" required>
                    <option value="">Choisissez un médecin</option>
                    <?php foreach ($medecins as $medecin): ?>
                        <option value="<?= htmlspecialchars($medecin['id']) ?>">
                            <?= htmlspecialchars($medecin['Nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Sélection de la date -->
            <div class="form-group">
                <label for="date">Date et heure du rendez-vous</label>
                <input type="datetime-local" id="date" name="date" class="form-control" required>
            </div>

            <!-- Sélection du type d'examen -->
            <div class="form-group">
                <label for="type_examen">Type d'examen</label>
                <select id="type_examen" name="type_examen" class="form-control" required>
                    <option value="">Choisissez un type d'examen</option>
                    <?php foreach ($examens as $examen): ?>
                        <option value="<?= htmlspecialchars($examen['id']) ?>">
                            <?= htmlspecialchars($examen['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Bouton de soumission -->
            <button type="submit" class="btn btn-primary w-100">Prendre rendez-vous</button>
        </form>

        <!-- Message de succès -->
        <?php if (!empty($_SESSION['message_success'])): ?>
            <div class="alert alert-success mt-3">
                <?= htmlspecialchars($_SESSION['message_success']) ?>
            </div>
            <?php unset($_SESSION['message_success']); ?>
        <?php endif; ?>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>
