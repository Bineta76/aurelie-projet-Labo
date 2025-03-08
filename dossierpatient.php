<?php include ("includes/header.php");?>

<div class="container">
    <div class="topnav">
        <a href="index.php" aria-label="Accueil">Accueil</a>
        <a href="inscription.php" aria-label="Inscription">Inscription</a>
        <a href="quiSommesNous.php" aria-label="Qui sommes-nous ?">Qui sommes-nous?</a>
        <a class="active" href="dossierpatient.php" aria-label="dossierpatient.php">Dossier patient</a>
        <a href="dossiermedical.php" aria-label="dossiermedical.php">Dossier medical</a>
        <a href="rdv.php" aria-label="Créer un rendez-vous">Créer un rendez-vous</a>
        <a href="listeRdv.php" aria-label="listeRdv">Liste des rendez vous patient</a>
        <a href="planningmedecin.php" aria-label="Voir le planning du médecin">Planning</a>
        <a href="centre.php" aria-label="Voir la liste des centres disponibles">Liste des centres</a>
        <a href="contactSupport.php" aria-label="contactSupport">Aide</a>
    </div>  

    <form>
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="datedenaissance">Date de naissance :</label>
            <input type="date" id="datedenaissance" name="datedenaissance" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="lieudenaissance">Lieu de naissance :</label>
            <input type="text" id="lieudenaissance" name="lieudenaissance" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="numerodesecuritesociale">Numéro de sécurité sociale :</label>
            <input type="text" id="numerodesecuritesociale" name="numerodesecuritesociale" class="form-control" maxlength="15" required>
        </div>
        <button type="submit" class="btn btn-primary">Soumettre</button>
    </form>
</div>

<?php include ("includes/footer.php");?>
