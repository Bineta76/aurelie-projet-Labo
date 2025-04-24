📁 1. calendrier_bootstrap.php
<?php
include 'includes/header.php';
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", "");

// Mois/année actuels
$mois = date('m');
$annee = date('Y');
$nbJours = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
$premierJour = date('N', strtotime("$annee-$mois-01"));

// Récupération des rendez-vous
$debutMois = "$annee-$mois-01";
$finMois = "$annee-$mois-$nbJours";

$stmt = $pdo->prepare("SELECT * FROM rdv WHERE date BETWEEN :debut AND :fin");
$stmt->execute([':debut' => $debutMois, ':fin' => $finMois]);

$rendezvousParJour = [];
while ($rdv = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $jour = date('j', strtotime($rdv['date']));
    $rendezvousParJour[$jour][] = $rdv;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendrier Bootstrap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .calendar-cell {
            height: 150px;
            border: 1px solid #dee2e6;
            padding: 5px;
            overflow-y: auto;
        }
        .rdv {
            background-color: #e0f7fa;
            border-left: 3px solid #007bff;
            padding: 5px;
            margin-bottom: 4px;
            border-radius: 4px;
            foat-size: 0.85em;
        }
        .day-number {
            font-weight: bold;
        }
    </style>
</head>
<body class="container my-4">
    <h2 class="mb-4 text-center">Calendrier des rendez-vous - <?php echo "$mois/$annee"; ?></h2>

    <div class="row fw-bold text-center mb-2">
        <?php
        $jours = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        foreach ($jours as $jour) {
            echo "<div class='col border'>$jour</div>";
        }
        ?>
    </div>

    <div class="row">
        <?php
        // Cases vides avant le 1er jour
        for ($i = 1; $i < $premierJour; $i++) {
            echo "<div class='col calendar-cell'></div>";
        }

        $jourSemaine = $premierJour;
        for ($jour = 1; $jour <= $nbJours; $jour++, $jourSemaine++) {
            echo "<div class='col calendar-cell'>";
            echo "<div class='day-number'>$jour</div>";

            if (isset($rendezvousParJour[$jour])) {
                foreach ($rendezvousParJour[$jour] as $rdv) {
                    echo "<div class='rdv'>
                            <div>{$rdv['heure']} - {$rdv['titre']} ({$rdv['nom']})</div>
                            <a href='supprimer.php?id={$rdv['id']}' class='btn btn-sm btn-danger mt-1' onclick=\"return confirm('Supprimer ce rendez-vous ?')\">Supprimer</a>
                          </div>";
                }
            }

            echo "</div>";

            if ($jourSemaine % 7 == 0) {
                echo "</div><div class='row'>";
            }
        }

        // Cases vides à la fin
        while ($jourSemaine % 7 != 1) {
            echo "<div class='col calendar-cell'></div>";
            $jourSemaine++;
        }
        ?>
    </div>
</body>
</html>
?>
📁 2. ajouter.php
<?php
ob_start();
$pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $date = $_POST['date'] ?? '';
    $heure = $_POST['heure'] ?? '';
    $nom = $_POST['nom'] ?? '';

    if (!empty($titre) && !empty($date) && !empty($heure) && !empty($nom)) {
        $dateFormat = 'Y-m-d';
        $d = DateTime::createFromFormat($dateFormat, $date);
        if ($d && $d->format($dateFormat) === $date) {
            $stmt = $pdo->prepare("INSERT INTO rdv (titre, date, heure, nom) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titre, $date, $heure, $nom]);

            // Vérification avant d'effectuer la redirection
            if (!headers_sent()) {
                header("Location: calendrier_bootstrap.php");
                exit;
            } else {
                echo "Erreur : Les en-têtes ont déjà été envoyés.";
            }
        } else {
            echo "Erreur : La date est invalide.";
        }
    } else {
        echo "Tous les champs doivent être remplis.";
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container my-4">
    <h2 class="mb-4">Ajouter un rendez-vous</h2>
    <form method="POST" class="row g-3">
        <div class="col-md-3">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>
        <div class="col-md-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="col-md-3">
            <label for="heure" class="form-label">Heure</label>
            <input type="time" class="form-control" id="heure" name="heure" required>
        </div>
        <div class="col-md-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Ajouter</button>
            <a href="calendrier_bootstrap.php" class="btn btn-secondary">Retour</a>
        </div>
    </form>
</body>
</html>
?>

📁 3. supprimer.php
<?php
// Démarre la mise en tampon de sortie dès le début
ob_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Affiche un message clair et arrête le script en cas d’erreur
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifie que l’ID est présent et numérique
if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Vérifie si l’ID existe dans la base
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rdv WHERE id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Supprime le rendez-vous
        $stmt = $pdo->prepare("DELETE FROM rdv WHERE id = ?");
        $stmt->execute([$id]);

        // Redirige vers la page calendrier si les en-têtes ne sont pas encore envoyés
        if (!headers_sent()) {
            header("Location: ./calendrier_bootstrap.php");

            exit;
        } else {
            echo "Erreur : Impossible de rediriger, les en-têtes sont déjà envoyés.";
        }
    } else {
        echo "ID introuvable.";
    }
} else {
    echo "ID invalide ou non fourni.";
}

// Fin du tampon
ob_end_flush();
?>
