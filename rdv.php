<?php
// Démarrer la session
session_start();

// Générer un jeton CSRF si inexistant
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Configuration de la base de données
$host = "localhost"; // Serveur
$user = "root";      // Utilisateur
$password = "";      // Mot de passe
$dbname = "labo";    // Nom de la base

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Gestion de l'envoi du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la vérification CSRF !");
    }

    // Récupérer et valider les données du formulaire
    $dateDebut = $_POST['date'] ?? '';
    $idMedecin = $_POST['medecin'] ?? '';
    $idExamen = $_POST['type_examen'] ?? '';
    $cabinetMedical = $_POST['centre'] ?? '';

    if (empty($dateDebut) || empty($idMedecin) || empty($idExamen) || empty($cabinetMedical)) {
        die("Tous les champs sont obligatoires !");
    }

    // Vérifier que les valeurs sont valides (optionnel mais recommandé)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $dateDebut)) {
        die("Date invalide !");
    }

    try {
        // Vérifier si le cabinet médical existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cabinet_medical WHERE id = ?");
        $stmt->execute([$cabinetMedical]);
        if ($stmt->fetchColumn() == 0) {
            die("Cabinet medical invalide !");
        }

        // Vérifier si le médecin existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM medecin WHERE id = ?");
        $stmt->execute([$idMedecin]);
        if ($stmt->fetchColumn() == 0) {
            die("Médecin invalide !");
        }

        // Vérifier si l'examen existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM examen WHERE id = ?");
        $stmt->execute([$idExamen]);
        if ($stmt->fetchColumn() == 0) {
            die("Type d'examen invalide !");
        }

        // Préparer et exécuter la requête SQL pour insérer le rendez-vous
        $sql = "INSERT INTO rendez_vous (date_debut, id_medecin, id_examen,id_cabinet_medical) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$dateDebut, $idMedecin, $idExamen, $cabinetMedical]);

        echo "Rendez-vous enregistré avec succès !";
    } catch (PDOException $e) {
        die("Erreur lors de l'enregistrement : " . htmlspecialchars($e->getMessage()));
    }
}

// Récupérer les données nécessaires pour les menus déroulants
$cabinetsMedical = $pdo->query("SELECT id, Nom FROM cabinet_medical")->fetchAll();
$medecins = $pdo->query("SELECT id, Nom, Prenom FROM medecin")->fetchAll();
$examens = $pdo->query("SELECT id, nom FROM examen")->fetchAll();
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre un rendez-vous</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    
    
    <div class="container">
        <h2>Prendre un rendez-vous</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="form-group">
                <label for="centre">Cabinet médical :</label>
                <select name="centre" id="centre" class="form-control" required>
                    <option value="">Sélectionnez un cabinet médical</option>
                    <?php foreach ($cabinetsMedical as $cabinet): ?>
                        <option value="<?php echo htmlspecialchars($cabinet['id']); ?>">
                            <?php echo htmlspecialchars($cabinet['Nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="medecin">Médecin :</label>
                <select name="medecin" id="medecin" class="form-control" required>
                    <option value="">Sélectionnez un médecin</option>
                    <?php foreach ($medecins as $medecin): ?>
                        <option value="<?php echo htmlspecialchars($medecin['id']); ?>">
                            <?php echo htmlspecialchars($medecin['Nom'] . ' ' . $medecin['Prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date">Date du rendez-vous :</label>
                <input type="datetime-local" name="date" id="date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="type_examen">Type d'examen :</label>
                <select name="type_examen" id="type_examen" class="form-control" required>
                    <option value="">Sélectionnez un type d'examen</option>
                    <?php foreach ($examens as $examen): ?>
                        <option value="<?php echo htmlspecialchars($examen['id']); ?>">
                            <?php echo htmlspecialchars($examen['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Prendre rendez-vous</button>
        </form>
        <?php include 'includes/footer.php';?>
    </div>

