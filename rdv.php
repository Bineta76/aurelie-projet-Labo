<?php
include 'includes/header.php';
session_start();
?>

<?php
// Afficher toutes les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Charger données pour les <select>
$examens = $pdo->query("SELECT id, nom FROM examen ORDER BY nom")->fetchAll();
$medecins = $pdo->query("SELECT id, nom, prenom FROM medecin ORDER BY nom, prenom")->fetchAll();
$cabinets = $pdo->query("SELECT id, nom_cabinet FROM cabinet_medical ORDER BY nom_cabinet")->fetchAll();

$success = '';
$errors = [];

// Enregistrement d'un RDV
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $medecin = $_POST['medecin'] ?? '';
    $examen = $_POST['examen'] ?? '';
    $cabinet = $_POST['cabinet_medical'] ?? '';

    if (!$date || !$medecin || !$examen || !$cabinet) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    if (!$errors) {
        try {
            $stmt = $pdo->prepare("INSERT INTO rdv (date, id_medecin, id_examen, id_cabinet_medical) VALUES (?, ?, ?, ?)");
            $stmt->execute([$date, $medecin, $examen, $cabinet]);
            $success = "✅ Rendez-vous enregistré avec succès.";
        } catch (PDOException $e) {
            $errors[] = "Erreur SQL : " . $e->getMessage();
        }
    }
}

// Récupération des rendez-vous
$sql = "
    SELECT r.date, m.prenom AS prenom_medecin, m.nom AS nom_medecin,
           e.nom AS nom_examen, c.nom_cabinet
    FROM rdv r
    JOIN medecin m ON r.id_medecin = m.id
    JOIN examen e ON r.id_examen = e.id
    JOIN cabinet_medical c ON r.id_cabinet_medical = c.id
    ORDER BY r.date DESC
";

$rendezvous = $pdo->query($sql)->fetchAll();
?>

<!-- ✅ HTML & Bootstrap -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <h1 class="mb-4 text-center">🩺 Gestion des Rendez-vous</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- 📝 Formulaire de RDV -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">➕ Prendre un nouveau rendez-vous</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Date / Heure</label>
                        <input type="datetime-local" name="date" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Médecin</label>
                        <select name="medecin" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($medecins as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Examen</label>
                        <select name="examen" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($examens as $e): ?>
                                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Cabinet</label>
                        <select name="cabinet_medical" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($cabinets as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom_cabinet']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 📋 Liste des Rendez-vous -->
    <h2 class="mb-3">📆 Rendez-vous enregistrés</h2>
    <?php if (!$rendezvous): ?>
        <div class="alert alert-warning">Aucun rendez-vous trouvé.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Médecin</th>
                    <th>Examen</th>
                    <th>Cabinet</th>
                    <th>Date / Heure</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rendezvous as $rdv): ?>
                    <tr>
                        <td><?= htmlspecialchars($rdv['prenom_medecin'] . ' ' . $rdv['nom_medecin']) ?></td>
                        <td><?= htmlspecialchars($rdv['nom_examen']) ?></td>
                        <td><?= htmlspecialchars($rdv['nom_cabinet']) ?></td>
                        <td><?= date('d/m/Y à H\hi', strtotime($rdv['date'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
