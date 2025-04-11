<?php
include 'includes/header.php';
session_start();

// Connexion DB avec gestion des erreurs
$pdo = new PDO("mysql:host=localhost;dbname=labo", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Mois/année actuels
$mois = date('m');
$annee = date('Y');
$nbJours = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);
$premierJour = date('N', strtotime("$annee-$mois-01"));

$mois = $mois -1;
// Récupérer rendez-vous
$debutMois = "$annee-$mois-01";
$finMois = "$annee-$mois-$nbJours";



$date = new DateTime();
$date->modify('-1 month');

$mois_fr = [
    1 => 'janvier', 'février', 'Mars', 'avril', 'mai', 'juin',
    'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
];


try {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE `date` BETWEEN :debut AND :fin");
    $stmt->execute([
        ':debut' => $debutMois,
        ':fin' => $finMois
    ]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Afficher un message si aucun résultat n'est trouvé
    if (empty($result)) {
        echo "Aucun résultat trouvé.";
    } else {
       # print_r($result);
    }
} catch (PDOException $e) {
    echo "Erreur SQL : " . $e->getMessage();
}
?>
Rdv 1 : Dr test - date 2025-03-01 ;
Rdv 2 : Dr Lepic -date 2025-03-25;
Rdv 3 : Dr Lepic - date 2025-03-22;
Rdv 4 : Dr Lafarge - date 2025-03-22;
Rdv 5 : Dr Lafarge  - date 2025-03-21;0
Rdv 6 :  Dr Lafarge- date 2025-03-21:
Rdv 7 : Dr Lepic - date 2025-03-05;
Rdv 8: Dr Lepic -date 2025-03-12;
Rdv 9: Dr Laville - date 2025-03-07;
Rdv 10: Dr Larfarge - date 2025-03-15;
Rdv 11: Dr Lepic - date 2025-03-16;
Rdv 12:Dr Lafarge - date 2025-03-17;
Rdv 13:Dr Lafarge - date 2025-03-18;
Rdv 14: Dr Lepic - date 2025-04-01;
Rdv 15: Dr Lafarge - date 2025-03-27;


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
    <h2 class="mb-4 text-center">Calendrier - <?php echo $mois_fr[(int)$date->format('n')] . ' ' . $date->format('Y'); ?></h2>
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
