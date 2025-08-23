<?php
include 'includes/header.php';
session_start();
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

// Fonction utilitaire
function fetchAllSafe(PDO $pdo, string $sql): array {
    try {
        $stmt = $pdo->query($sql);
        return $stmt ? $stmt->fetchAll() : [];
    } catch (PDOException $e) {
        error_log("Erreur SQL dans fetchAllSafe : " . $e->getMessage());
        return [];
    }
}



// Chargement des listes déroulantes
$examens  = fetchAllSafe($pdo, "SELECT id, nom_examen FROM examen ORDER BY nom_examen");
$medecins = fetchAllSafe($pdo, "SELECT id, nom, prenom FROM medecin ORDER BY nom, prenom");
$cabinets = fetchAllSafe($pdo, "SELECT id, nom_cabinet FROM cabinet_medical ORDER BY nom_cabinet");

$success = '';
$errors  = [];

$date    = '';
$medecin = '';
$examen  = '';
$cabinet = '';

// Enregistrement d'un RDV
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date    = trim($_POST['date'] ?? '');
    $medecin = trim($_POST['medecin'] ?? '');
    $examen  = trim($_POST['examen'] ?? '');
    $cabinet = trim($_POST['cabinet_medical'] ?? '');

    if (!$date || !$medecin || !$examen || !$cabinet) {
        $errors[] = "Tous les champs sont obligatoires.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $date)) {
        // Format attendu par datetime-local (sans secondes)
        $errors[] = "Le format de la date est invalide.";
    }

    if (!$errors) {
        // Convertir en format DATETIME MySQL (avec secondes)
        $date_sql = str_replace('T', ' ', $date) . ':00';

        try {
            $stmt = $pdo->prepare("
                INSERT INTO rdv (date, id_medecin, id_examen, id_cabinet_medical)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$date_sql, $medecin, $examen, $cabinet]);
            $success = "✅ Rendez-vous enregistré avec succès.";

            // Réinitialiser les valeurs pour le formulaire
            $date = $medecin = $examen = $cabinet = '';
        } catch (PDOException $e) {
            $errors[] = "Erreur SQL : " . $e->getMessage();
        }
    }
}

// Récupération des rendez-vous
try {
    $sql = "
        SELECT 
            r.id,
            e.nom,
            m.nom   AS nom_medecin,
            m.prenom AS prenom_medecin,
            c.nom_cabinet,
            r.date AS date_rdv
        FROM rdv r
        JOIN examen e ON r.id_examen = e.id
        JOIN medecin m ON r.id_medecin = m.id
        JOIN cabinet_medical c ON r.id_cabinet_medical = c.id
        ORDER BY r.date
    ";
    $stmt = $pdo->query($sql);
    $rendezvous = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur SQL lors de la récupération des rendez-vous : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion des Rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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

    <!-- Formulaire -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">➕ Prendre un nouveau rendez-vous</div>
        <div class="card-body">
            <form method="POST" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="date">Date / Heure</label>
                        <input type="datetime-local" id="date" name="date" class="form-control" 
                               value="<?= htmlspecialchars($date) ?>" required />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="medecin">Médecin</label>
                        <select name="medecin" id="medecin" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($medecins as $m): ?>
                                <option value="<?= $m['id'] ?>" <?= ($medecin == $m['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['prenom'].' '.$m['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="examen">Examen</label>
                        <select name="examen" id="examen" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($examens as $e): ?>
                                <option value="<?= $e['id'] ?>" <?= ($examen == $e['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($e['nom_examen']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cabinet_medical">Cabinet</label>
                        <select name="cabinet_medical" id="cabinet_medical" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($cabinets as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= ($cabinet == $c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nom_cabinet']) ?>
                                </option>
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

    <!-- Liste -->
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
                        <td><?= htmlspecialchars($rdv['prenom_medecin'].' '.$rdv['nom_medecin']) ?></td>
                        <td><?= htmlspecialchars($rdv['nom']) ?></td>
                        <td><?= htmlspecialchars($rdv['nom_cabinet']) ?></td>
                        <td><?= date('d/m/Y à H\hi', strtotime($rdv['date_rdv'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
