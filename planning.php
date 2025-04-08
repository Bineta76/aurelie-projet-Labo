<?php
include 'includes/header.php';
session_start();

// Connexion à MySQL
try {
    $pdo = new PDO('mysql:host=localhost;dbname=labo;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<p style='color:red;'>Erreur de connexion à la base de données : " . htmlspecialchars($e->getMessage()) . "</p>");
}

// Définition de l'année et des mois
$annee = 2025;
$nombre_mois_suivants = 2;

// Initialisation des formatteurs de date en français
$formatterMois = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
$formatterJour = new IntlDateFormatter('fr_FR', IntlDateFormatter::FULL, IntlDateFormatter::NONE); // Format complet en français

// Tableau des jours de la semaine
$joursSemaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

// Définition des couleurs par type d'événement
$couleursEvenements = [
    "Docteur Lepic" => "red",
    "Docteur Lafarge" => "blue",
    "Docteur Laville" => "purple",
    "Docteur Leparc" => "orange"
];

// Styles CSS
echo "<style>
    table { width: 100%; text-align: center; border-collapse: collapse; }
    th { background-color: #3f51b5; color: white; padding: 10px; }
    td { padding: 10px; border: 1px solid #ccc; }
    .weekend { background-color: #ffcccc; }
    .event-day { background-color: #ccffcc; }
</style>";

// Fonction pour récupérer les événements
function getEvents($pdo, $mois, $annee) {
    try {
        $stmt = $pdo->prepare("SELECT date, time, title FROM events WHERE MONTH(date) = :mois AND YEAR(date) = :annee");
        $stmt->execute(['mois' => $mois, 'annee' => $annee]);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $eventList = [];
        foreach ($events as $event) {
            $eventList[$event['date']] = [
                'time' => htmlspecialchars($event['time']),
                'title' => htmlspecialchars($event['title'])
            ];
        }
        return $eventList;
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Erreur lors de la récupération des événements : " . htmlspecialchars($e->getMessage()) . "</p>";
        return [];
    }
}

// Fonction pour afficher le calendrier
function afficherCalendrierMois($mois, $annee, $formatterMois, $formatterJour, $pdo, $joursSemaine, $couleursEvenements) {
    $monthName = ucfirst($formatterMois->format(new DateTime("$annee-$mois-01")));
    $events = getEvents($pdo, $mois, $annee);

    // Vérification du premier jour du mois en français
    echo "<p>Premier jour du mois ($mois/$annee) : " . $formatterJour->format(new DateTime("$annee-$mois-01")) . "</p>";

    echo "<h2 style='margin-top: 20px;'>Calendrier de $monthName</h2>";
    echo "<table><tr>";

    foreach ($joursSemaine as $jour) {
        echo "<th>$jour</th>";
    }
    echo "</tr><tr>";

    for ($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $mois, $annee); $day++) {
        $date = sprintf('%04d-%02d-%02d', $annee, $mois, $day);
        $jourNumero = date('N', strtotime($date));

        // Ajout des classes CSS
        $classeJour = in_array($jourNumero, [6, 7]) ? "weekend" : "";
        if (isset($events[$date])) {
            $classeJour .= " event-day";
        }

        echo "<td class='$classeJour'>$day<br>";

        // Affichage des événements avec couleur
        if (isset($events[$date])) {
            $titreEvent = $events[$date]['title'];
            $couleurEvent = "black";

            foreach ($couleursEvenements as $type => $couleur) {
                if (stripos($titreEvent, $type) !== false) {
                    $couleurEvent = $couleur;
                    break;
                }
            }

            echo "<br><span style='color:$couleurEvent; font-weight:bold;'>📅 {$events[$date]['time']} - $titreEvent</span>";
        }

        echo "</td>";

        if ($jourNumero == 7) {
            echo "</tr><tr>";
        }
    }
    echo "</tr></table>";
}

// Affichage des mois précédents, actuels et suivants
afficherCalendrierMois(date("m", strtotime("-1 month")), $annee, $formatterMois, $formatterJour, $pdo, $joursSemaine, $couleursEvenements);
afficherCalendrierMois(date("m"), $annee, $formatterMois, $formatterJour, $pdo, $joursSemaine, $couleursEvenements);


for ($i = 1; $i <= $nombre_mois_suivants; $i++) {
    afficherCalendrierMois(date("m", strtotime("+$i month")), $annee, $formatterMois, $formatterJour, $pdo, $joursSemaine, $couleursEvenements);
}
?>