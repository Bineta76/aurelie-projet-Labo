<?php 
include("includes/header.php");
include("includes/db.php"); // Fichier contenant la connexion PDO
session_start();
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'create'){

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
            header("Location: dossier_medical.php");
            exit();
        } catch (PDOException $e) {
            echo "Erreur lors de l'ajout du dossier : " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "<p style='color:red;'>Tous les champs sont obligatoires.</p>";
    }
}

// Supprimer un dossier (section supprimée mal placée et doit être corrigée)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete'){
    $id = $_POST['id'];
    if (!empty($id)) {
        try {
            $sql = "DELETE FROM dossier_medical WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            echo "Dossier supprimé avec succès.";
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression du dossier : " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "<p style='color:red;'>ID manquant pour supprimer le dossier.</p>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $date = $_POST['date'];
    $compte_rendu = trim($_POST['compte_rendu']);
    $medecin = trim($_POST['medecin']);

    if (!empty($id) && !empty($nom) && !empty($prenom) && !empty($date) && !empty($compte_rendu) && !empty($medecin)) {
        try {
            $sql = "UPDATE dossier_medical SET nom = :nom, prenom = :prenom, date = :date, compte_rendu = :compte_rendu, medecin = :medecin WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':date' => $date,
                ':compte_rendu' => $compte_rendu,
                ':medecin' => $medecin
            ]);
            echo "Dossier mise à jour avec succès.";
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du dossier : " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "<p style='color:red;'>ID manquant pour mise à jour du dossier.</p>";
    }
}

$sql = 'SELECT * FROM dossier_medical';
$stmt = $pdo->prepare($sql);
$stmt->execute();

$dossiersMedicaux = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mt-5">
    <h1 class="text-center mt-4 mb-4">Dossier Médical</h1>

    <!-- Affichage des messages d'erreur/succès -->
    <?php if (isset($_SESSION['message_error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['message_error']; unset($_SESSION['message_error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['message_success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['message_success']; unset($_SESSION['message_success']); ?>
        </div>
    <?php endif; ?>

    
    <h2>Création d'un dossier médical :</h2>
    <form method="post" class="mt-4">
        <input type="hidden" name="action" value="create">
        <div class="form-group mb-3">
            <label for="nom">nom :</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="prenom">prénom :</label>
            <input type="text" id="prenom" name="prenom" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="date">date :</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="compte_rendu">compte rendu :</label>
            <textarea id="compte_rendu" name="compte_rendu" class="form-control" rows="4"></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="medecin">médecin :</label>
            <input type="text" id="medecin" name="medecin" class="form-control">
         
        </div>
       <!-- formulaire de création -->
      
        <button type="submit" class="btn btn-info">Créer</button>
        <button type="submit" class="btn btn-info">Supprimer</button>
        <button type="submit" class="btn btn-info">Mise à jour</button>
    </form>

    <div class="my-5 row">
        <?php foreach($dossiersMedicaux as $dossierMedical) : ?>
            <div class="col-12 col-md-6 my-5">
                <h2>Modifier le dossier médical : <?= $dossierMedical['nom'] ?></h2>
                <form method="post" class="mt-4">
                    <input type="hidden" name="id" value="<?= $dossierMedical['id'] ?>">
                    <div class="form-group mb-3">
                        <label for="nom">nom :</label>
                        <input type="text" id="nom" name="nom" value="<?= $dossierMedical['nom'] ?>" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="prenom">prénom :</label>
                        <input type="text" id="prenom" name="prenom" value="<?= $dossierMedical['prenom'] ?>" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="date">date :</label>
                        <input type="date" id="date" name="date" value="<?= $dossierMedical['date'] ?>" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="compte_rendu">compte rendu :</label>
                        <textarea id="compte_rendu" name="compte_rendu" class="form-control" rows="4">
                            <?= $dossierMedical['compte_rendu'] ?>
                        </textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="medecin">médecin :</label>
                        <input type="text" id="medecin" name="medecin" value="<?= $dossierMedical['medecin'] ?>" class="form-control">
                    </div>
                    <div class="d-flex gap-5 justify-content-center">
                    
                    </div>
                </form>
            </div>
        <?php endforeach ?>
    </div>
</div>