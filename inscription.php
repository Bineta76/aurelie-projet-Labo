<?php
session_start();
include 'includes/header.php';
include 'includes/db.php'; // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et validation des données du formulaire
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $numerodesecuritesociale = htmlspecialchars(trim($_POST['numerodesecuritesociale']));
    $mdp = trim($_POST['mdp']);
    // Vérification des champs obligatoires
    if (!$nom || !$prenom || !$email || !$numerodesecuritesociale || !$mdp) {
        $_SESSION['message_error'] = "Tous les champs sont obligatoires.";
        header("Location: inscription.php");
        exit();
    }
    // Vérification de la longueur du numéro de sécurité sociale
    if (!preg_match('/^[0-9]{15}$/', $numerodesecuritesociale)) {
        $_SESSION['message_error'] = "Le numéro de sécurité sociale doit contenir exactement 15 chiffres.";
        header("Location: inscription.php");
        exit();
    }
    // Vérification de la longueur du mot de passe
    if (strlen($mdp) < 8) {
        $_SESSION['message_error'] = "Le mot de passe doit contenir au moins 8 caractères.";
        header("Location: inscription.php");
        exit();
    }
    // Hachage du mot de passe sécurisé
    $mdp_hashed = password_hash($mdp, PASSWORD_BCRYPT);
    // Vérification des doublons (email ou numéro de sécurité sociale déjà utilisé)
    $stmt = $pdo->prepare("SELECT id FROM patient WHERE email = :email OR numerodesecuritesociale = :nss");
    $stmt->execute([':email' => $email, ':nss' => $numerodesecuritesociale]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['message_error'] = "Cet email ou numéro de sécurité sociale est déjà utilisé.";
        header("Location: inscription.php");
        exit();
    }
    // Insertion des données dans la base de données
    $stmt = $pdo->prepare("INSERT INTO patient (nom, prenom, email, numerodesecuritesociale, mdp) VALUES (:nom, :prenom, :email, :nss, :mdp)");
    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':email' => $email,
        ':nss' => $numerodesecuritesociale,
        ':mdp' => $mdp_hashed,
    ]);
    $_SESSION['message_success'] = "Inscription réussie !";
    header("Location: index.php");
    exit();
}
?>

<div class="container mt-5">
    <nav class="topnav" aria-label="Navigation principale">
        <a href="index.php">Accueil</a>
        <a href="inscription.php" class="active">Inscription</a>
        <a href="quiSommesNous.php">Qui sommes-nous ?</a>
        <a href="dossierpatient.php">Dossier patient</a>
        <a href="listeRdv.php">Liste des rendez vous patient</a>
        <a href="rdv.php">Créer un rendez-vous</a>
        <a href="planningmedecin.php">Planning</a>
        <a href="centre.php">Liste des centres</a>
        <a href="contactSupport.php">Aide</a>
    </nav>

    <h1 class="text-center mt-4 mb-4">Inscription</h1>

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

    <form method="POST" action="inscription.php">
        <div class="form-group mb-3">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="numerodesecuritesociale">Numéro de sécurité sociale :</label>
            <input type="text" id="numerodesecuritesociale" name="numerodesecuritesociale" class="form-control" maxlength="15" required pattern="[0-9]{15}" title="Le numéro de sécurité sociale doit contenir 15 chiffres">
        </div>
        <div class="form-group mb-3">
            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp" class="form-control" required minlength="8">
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-3">S'inscrire</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>