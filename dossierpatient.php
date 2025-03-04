<?php include ("includes/header.php");?>
   
<div class="container">
        <div class="topnav">
            <a href="index.php" aria-label="Accueil">Accueil</a>
            <a href="inscription.php" aria-label="Inscription">Inscription</a>
            <a href="quiSommesNous.php" aria-label="Qui sommes-nous ?">Qui sommes-nous?</a>
            <a class="active"href="dossierpatient.php" aria-label="dossierpatient.php">Dossier patient</a>
            <a href="dossiermedical.php" aria-label="dossiermedical.php">Dossier medical</a>
            <a href="rdv.php" aria-label="Créer un rendez-vous">Créer un rendez-vous</a>
            <a href="historiqueRdv.php" aria-label="historiqueRdv">Historique Rendez-vous</a>
            <a href="planningmedecin.php" aria-label="Voir le planning du médecin">Planning</a>
            <a href="centre.php" aria-label="Voir la liste des centres disponibles">Liste des centres</a>
            <a href="contactSupport.php" aria-label="contactSupport">Aide </a>   
            </a>
        </div>  

  <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="login">login:</label>
                <input type="text" id="login" name="login" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="numerodesecuritesociale">Numéro de sécurité sociale :</label>
                <input type="text" id="numerodesecuritesociale" name="numerodesecuritesociale" class="form-control" maxlength="15" required>
            </div>
            <div class="form-group">
                <label for="mdp">Mot de passe :</label>
                <input type="password" id="mdp" name="mdp" class="form-control" required>
            </div>
            