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
            font-size: 0.85em;
        }
        .day-number {
            font-weight: bold;
        }
    </style>
</head>
<body class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center">Calendrier des rendez-vous - <?php echo "$mois/$annee"; ?></h2>
        <a href="./ajouter.php" class="btn btn-success">Ajouter un rendez-vous</a>
    </div>

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
                            <a href='./supprimer.php?id={$rdv['id']}' class='btn btn-sm btn-danger mt-1' onclick=\"return confirm('Supprimer ce rendez-vous ?')\">Supprimer</a>
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
