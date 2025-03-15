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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Creer'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $date = $_POST['date'];
    $compte_rendu = trim($_POST['compte_rendu']);
    $medecin = trim($_POST['medecin']);

    if (!empty($nom) && !empty($prenom) && !empty($date) && !empty($compte_rendu) && !empty($medecin)) {
        try {
            $sql = "INSERT INTO dossier_medical (nom, prenom, date, compte_rendu, medecin) VALUES (:nom, :prenom, STR_TO_DATE(:date,'%Y,%M,%D), :compte_rendu, :medecin)";
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

// Supprimer un dossier (section supprimée mal placée et doit être corrigée)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Supprimer'])) {
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
require_once 'require.php';
require_once 'includes/header.php';
?>
<div class="container mt-5">
   

    <h1 class="text-center mt-4 mb-4">Dossier medical</h1>

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

    <form method="POST" action="Creer">
        <div class="form-group mb-3">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="email">Date :</label>
            <input type="text" id="date" name="date" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="compte_rendu">Compte rendu :</label>
            <input type="text" id="compte_rendu" name="compte_rendu" class="form-control">
        </div>
        <div class="form-group mb-3">
        <label for="medecin">Medecin :</label>
        <input type="text" id="medecin" name="medecin"class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-3">Créer</button>
    </form>
    <form method="POST" action="Supprimer">
        <button type="submit" class="btn btn-primary w-100 mt-3">Supprimer</button>
    </form>
    <?php include 'includes/footer.php';?> 
</div>
