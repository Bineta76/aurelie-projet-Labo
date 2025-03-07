<?php include ("includes/header.php");?>
   
<?php include ("includes/header.php");?>
   
<div class="container">
    <div class="topnav">
        <a href="index.php" aria-label="Accueil">Accueil</a>
        <a href="inscription.php" aria-label="Inscription">Inscription</a>
        <a href="quiSommesNous.php" aria-label="Qui sommes-nous ?">Qui sommes-nous?</a>
        <a href="dossierpatient.php" aria-label="dossierpatient.php">Dossier patient</a>
        <a class="active" href="dossiermedical.php" aria-label="dossiermedical.php">Dossier medical</a>
        <a href="rdv.php" aria-label="Créer un rendez-vous">Créer un rendez-vous</a>
        <a href="planningmedecin.php" aria-label="Voir le planning du médecin">Planning</a>
        <a href="centre.php" aria-label="Voir la liste des centres disponibles">Liste des centres</a>
        <a href="contactSupport.php" aria-label="contactSupport">Aide</a>   
    </div>  

    <div class="form-group">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" class="form-control" required>
    </div>

    <table class="table">
        <caption>
            <h3 class="text-center">Dossier medical</h3>
        </caption>
        <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Compte rendu echographie</th>
                <th scope="col">Medecin</th>
            </tr>
        </thead>
        <tbody>
            <!-- Vos lignes de tableau ici, avec les corrections -->
            <tr>
                <th scope="row">2025-02-28</th>
                <td>la patiente présente des kystes dans le corps.Traitement donné: Pillule</td>
                <td>Docteur Lafarge</td>
            </tr>
            <!-- ... autres lignes ... -->
        </tbody>
    </table>
</div>
