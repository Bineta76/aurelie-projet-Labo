<?php
// Connexion à MySQL avec gestion des erreurs
try {
    $pdo = new PDO('mysql:host=localhost;dbname=labo;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Définition du mois et de l'année avec navigation
$mois = isset($_GET['mois']) ? intval($_GET['mois']) : date('m');
$annee = isset($_GET['annee']) ? intval($_GET['annee']) : date('Y');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $mois, $annee);

// Formatage du nom du mois en français
$formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
$monthName = ucfirst($formatter->format(new DateTime("$annee-$mois-01")));

// Récupération des événements du mois
$stmt = $pdo->prepare("SELECT date, title FROM events WHERE MONTH(date) = :mois AND YEAR(date) = :annee");
$stmt->execute(['mois' => $mois, 'annee' => $annee]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stocker les événements dans un tableau indexé par date
$eventList = [];
foreach ($events as $event) {
    $eventList[$event['date']] = htmlspecialchars($event['title']);
}

// Affichage du calendrier
echo "<h2>Calendrier de $monthName $annee</h2>";
echo "<ul style='list-style-type:none; padding:0;'>";

for ($day = 1; $day <= $daysInMonth; $day++) {
    $date = sprintf('%04d-%02d-%02d', $annee, $mois, $day);
    $dayName = ucfirst($formatter->format(new DateTime($date)));

    // Définition de la couleur des jours (rouge pour le week-end)
    $couleur = in_array(date('N', strtotime($date)), [6, 7]) ? 'red' : 'black';

    echo "<li style='padding:10px; border-bottom:1px solid #ccc;'>";
    echo "<strong style='color:$couleur;'>$dayName $day</strong>";

    // Afficher les événements s'il y en a
    if (isset($eventList[$date])) {
        echo "<br><span style='color:blue;'>" . $eventList[$date] . "</span>";
    }

    echo "</li>";
}

echo "</ul>";

// Liens de navigation entre les mois
echo "<a href='?mois=" . ($mois - 1) . "&annee=$annee'>← Mois précédent</a> | ";
echo "<a href='?mois=" . ($mois + 1) . "&annee=$annee'>Mois suivant →</a>";
?>

<!-- Formulaire pour sélectionner un mois -->
<h3>Choisir un mois</h3>
<form method="GET" action="">
    <label>Choisir un mois :</label>
    <select name="mois">
        <?php for ($m = 1; $m <= 12; $m++) { ?>
            <option value="<?= $m ?>" <?= ($m == $mois) ? 'selected' : '' ?>>
                <?= ucfirst((new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE))->format(new DateTime("2025-$m-01"))) ?>
            </option>
        <?php } ?>
    </select>
    
    <label>Année :</label>
    <input type="number" name="annee" value="<?= $annee ?>" min="2000" max="2100">
    <button type="submit">Afficher</button>
</form>

<!-- Formulaire pour ajouter un événement -->
<h3>Ajouter un événement</h3>
<form action="add_event.php" method="post">
    <label>Date :</label>
    <input type="date" name="date" required>
    <label>Titre :</label>
    <input type="text" name="title" required>
    <button type="submit">Ajouter</button>
</form>

<?php
// Script pour ajouter un événement
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO events (date, title) VALUES (:date, :title)");
        $stmt->execute(['date' => $_POST['date'], 'title' => $_POST['title']]);
        header("Location: calendrier.php"); // Redirection vers le calendrier
        exit();
    } catch (PDOException $e) {
        die("Erreur d'insertion : " . $e->getMessage());
    }
}
?>