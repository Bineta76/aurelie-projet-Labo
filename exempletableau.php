// Exemple d'un tableau associatif
$personnes = array(
    array('Nom' => 'Dupont', 'Prénom' => 'Jean', 'Âge' => 30),
    array('Nom' => 'Durand', 'Prénom' => 'Marie', 'Âge' => 25),
    array('Nom' => 'Martin', 'Prénom' => 'Paul', 'Âge' => 40)
);
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau PHP</title>
    <style>
        table {
            border-collapse: collapse;
            width: 50%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

<?php
// Affichage de la table HTML
echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th>Nom</th>";
echo "<th>Prénom</th>";
echo "<th>Âge</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

foreach ($personnes as $personne) {
    echo "<tr>";
    echo "<td>" . $personne['Nom'] . "</td>";
    echo "<td>" . $personne['Prénom'] . "</td>";
    echo "<td>" . $personne['Âge'] . "</td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
?>

</body>
</html>
