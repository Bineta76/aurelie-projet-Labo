<?php
session_start();
include 'includes/header.php'; // Chargement du header

// Connexion à la base de données
$host = "localhost";            
$dbname = "labo";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . htmlspecialchars($e->getMessage()));
}

// Récupérer les informations des patients
try {
    $stmt = $pdo->prepare("SELECT * FROM patient");
    $stmt->execute();
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<p class='error'>Erreur lors de la récupération des patients : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}

// Récupérer les informations des dossiers médicaux
try {
    $stmt = $pdo->prepare("SELECT * FROM dossier_medical");
    $stmt->execute();
    $dossiers_medical = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<p class='error'>Erreur lors de la récupération des dossiers médicaux : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}
?>

<h1>Liste des patients et leurs dossiers médicaux</h1>

<!-- Table pour afficher les patients -->
<h2>Patients</h2>
<Table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Numéro de sécurité sociale</th>
            <th>Email</th>
            <th>Date de naissance</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($patients as $patient): ?>
            <tr>
                <td><?= htmlspecialchars($patient['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($patient['nom'] ?? '') ?></td>
                <td><?= htmlspecialchars($patient['prenom'] ?? '') ?></td>
                <td><?= htmlspecialchars($patient['numero_de_securite_sociale'] ?? '') ?></td>
                <td><?= htmlspecialchars($patient['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($patient['date_de_naissance'] ?? '') ?></td>
                <td>
                    <a href="modifier_patient.php?id=<?= htmlspecialchars($patient['id']) ?>">Modifier</a> |
                    <a href="copier_patient.php?id=<?= htmlspecialchars($patient['id']) ?>">Copier</a> |
                    <a href="supprimer_patient.php?id=<?= htmlspecialchars($patient['id']) ?>">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Table pour afficher les dossiers médicaux -->
<h2>Dossiers Médicaux</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Date</th>
            <th>Compte Rendu</th>
            <th>Médecin</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dossiers_medical as $dossier): ?>
            <tr>
                <td><?= htmlspecialchars($dossier['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($dossier['nom'] ?? '') ?></td>
                <td><?= htmlspecialchars($dossier['prenom'] ?? '') ?></td>
                <td><?= htmlspecialchars($dossier['date'] ?? '') ?></td>
                <td><?= htmlspecialchars($dossier['compte_rendu'] ?? '') ?></td>
                <td><?= htmlspecialchars($dossier['medecin'] ?? '') ?></td>
                <td>
                    <a href="modifier_dossier_medical.php?id=<?= htmlspecialchars($dossier['id']) ?>">Modifier</a> |
                    <a href="copier_dossier_medical.php?id=<?= htmlspecialchars($dossier['id']) ?>">Copier</a> |
                    <a href="supprimer_dossier_medical.php?id=<?= htmlspecialchars($dossier['id']) ?>">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
// Affichage des erreurs si présentes
if (isset($_SESSION['error'])) {
    echo "<p class='error'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
?>
