<?php 
include("includes/header.php");
include("includes/db.php"); // Fichier contenant la connexion PDO

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=labo', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Ajouter un dossier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $date = $_POST['date'];
    $compte_rendu = trim($_POST['compte_rendu']);
    $medecin = trim($_POST['medecin']);

    if (!empty($nom) && !empty($prenom) && !empty($date) && !empty($compte_rendu) && !empty($medecin)) {
        try {
            $sql = "INSERT INTO dossier_medical (nom, prenom, date, compte_rendu, medecin) VALUES (:nom, :prenom, :date, :compte_rendu, :medecin)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':date' => $date,
                ':compte_rendu' => $compte_rendu,
                ':medecin' => $medecin
            ]);
            // Redirection après ajout
            header("Location: dossiermedical.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur lors de l'ajout du dossier : " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "<p style='color:red;'>Tous les champs sont obligatoires.</p>";
    }
}

// Supprimer un dossier
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM dossier_medical WHERE id = :id");
            $stmt->execute([':id' => $id]);
            // Redirection après suppression
            header("Location: dossiermedical.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression du dossier : " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "<p style='color:red;'>ID non valide.</p>";
    }
}

// Récupérer les dossiers (filtrer par nom et prénom si nécessaire)
$nom = $_GET['nom'] ?? null;
$prenom = $_GET['prenom'] ?? null;
$dossiers = [];

try {
    if (!empty($nom) && !empty($prenom)) {
        $sql = "SELECT * FROM dossier_medical WHERE nom = :nom AND prenom = :prenom ORDER BY date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nom' => trim($nom), ':prenom' => trim($prenom)]);
    } elseif (!empty($nom)) {
        $sql = "SELECT * FROM dossier_medical WHERE nom = :nom ORDER BY date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nom' => trim($nom)]);
    } else {
        // Si aucun filtre n'est appliqué, récupérer tous les dossiers
        $sql = "SELECT * FROM dossier_medical ORDER BY date DESC";
        $stmt = $pdo->query($sql);
    }
    // Récupération des résultats
    $dossiers = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des dossiers : " . htmlspecialchars($e->getMessage());
}
?>

<div class="container">
    <div class="topnav">
        <a href="index.php">Accueil</a>
        <a href="inscription.php">Inscription</a>
        <a href="quiSommesNous.php">Qui sommes-nous?</a>
        <a href="dossierpatient.php">Dossier patient</a>
        <a class="active" href="dossiermedical.php">Dossier médical</a>
        <a href="rdv.php">Créer un rendez-vous</a>
        <a href="planningmedecin.php">Planning</a>
        <a href="centre.php">Liste des centres</a>
        <a href="contactSupport.php">Aide</a>
    </div>

    <h3 class="text-center">Ajouter un dossier médical</h3>
    <form method="POST" action="">
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="date">Date :</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="compte_rendu">Compte rendu :</label>
            <textarea id="compte_rendu" name="compte_rendu" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label for="medecin">Médecin :</label>
            <input type="text" id="medecin" name="medecin" class="form-control" required>
        </div>
        <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
    </form>

    <h3 class="text-center mt-4">Dossier médical</h3>
    <?php if (!empty($dossiers)) { ?>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Compte rendu</th>
                <th>Médecin</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dossiers as $dossier) { ?>
                <tr>
                    <td><?= htmlspecialchars($dossier['date']); ?></td>
                    <td><?= htmlspecialchars($dossier['compte_rendu']); ?></td>
                    <td><?= htmlspecialchars($dossier['medecin']); ?></td>
                    <td>
                        <a href="modifier.php?id=<?= htmlspecialchars($dossier['id']); ?>" class="btn btn-warning">Modifier</a>
                        <a href="?delete=<?= htmlspecialchars($dossier['id']); ?>" class="btn btn-danger" onclick="return confirm('Voulez-vous supprimer ce dossier ?');">Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } else { ?>
      <p>Aucun dossier trouvé.</p>
    <?php } ?>
</div>
