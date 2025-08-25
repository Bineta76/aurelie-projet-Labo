<?php
// Afficher les erreurs pour debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion à la base
$host = "localhost";
$user = "root";
$password = "";
$dbname = "labo";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour récupérer tous les examens ou rendez-vous
    $sql = "SELECT * FROM examen"; // Remplace 'examen' par le nom exact de ta table
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Vérifier si des données existent
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($results) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>";
        // Afficher les noms de colonnes automatiquement
        foreach(array_keys($results[0]) as $col) {
            echo "<th>" . htmlspecialchars($col) . "</th>";
        }
        echo "</tr>";

        // Afficher les données
        foreach($results as $row) {
            echo "<tr>";
            foreach($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "Aucun examen trouvé.";
    }

} catch(PDOException $e) {
    echo "Erreur SQL : " . $e->getMessage();
}

$conn = null;
?>
