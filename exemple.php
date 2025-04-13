<?php
// Connexion DB
$pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8";"root";"");

// Mois/année actuels
$mois = date('m');
$annee = date('Y');
$nbJours = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
$premierJour = date('N', strtotime("$annee-$mois-01"));

// Récup rendez-vous
$debutMois = "$annee-$mois-01";
$finMois = "$annee-$mois-$nbJours";

$stmt = $pdo->prepare("SELECT * FROM events WHERE date_rdv BETWEEN :debut AND :fin");
$stmt->execute([':debut' => $debutMois, ':fin' => $finMois]);

$rendezvousParJour = [];
while ($rdv = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $jour = date('j', strtotime($rdv['date_rdv']));
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
            height: 120px;
            border: 1px solid #dee2e6;
            padding: 5px;
            overflow-y: auto;
        }
        .rdv {
            background-color: #e0f7fa;
            border-left: 3px solid #007bff;
            padding: 3px 5px;
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
    <h2 class="mb-4 text-center">Calendrier - <?php echo date('F Y'); ?></h2>
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
                    echo "<div class='rdv'>{$rdv['heure']} - {$rdv['titre']}</div>";
                }
            }

            echo "</div>";

            if ($jourSemaine % 7 == 0) {
                echo "</div><div class='row'>";
            }
        }

        // Cases vides après le dernier jour
        while ($jourSemaine % 7 != 1) {
            echo "<div class='col calendar-cell'></div>";
            $jourSemaine++;
        }
        ?>
    </div>
</body>
</html>