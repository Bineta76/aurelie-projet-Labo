<?php include 'includes/header.php'; ?>

<div class="container">
    <!-- Navigation Bootstrap -->
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Mon Application</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav">
                <li><a href="Accueil.php" aria-label="Accueil">Accueil</a></li>
                    <li><a href="inscription.php" aria-label="Inscription">Inscription</a></li>
                    <li><a href="quiSommesNous.php" aria-label="Qui sommes-nous ?">Qui sommes-nous?</a></li>
                    <li><a href="dossierpatient.php" aria-label="Dossier patient">Dossier patient</a></li>
                    <li><a href="dossiermedical.php" aria-label="Dossier médical">Dossier médical</a></li>
                    <li><a href="rdv.php" aria-label="Créer un rendez-vous">Créer un rendez-vous</a></li>
                    <li><a href="planningmedecin.php" aria-label="Voir le planning du médecin">Planning</a></li>
                    <li class="active"><a href="centre.php" aria-label="Voir la liste des centres disponibles">Liste des centres</a></li>
                    <li><a href="contactSupport.php" aria-label="Contact support">Aide</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Section principale -->
    <h3>Laboratoires disponibles</h3>

    <!-- Galerie d'images avec Bootstrap -->
    <div class="row">
        <!-- Image 1 -->
        <div class="col-sm-4">
            <img src="assets/images/bordeaux.jpg" alt="Bordeaux" class="img-responsive img-thumbnail" style="width:100%; height:200px;">
        </div>

        <!-- Image 2 -->
        <div class="col-sm-4">
            <img src="assets/images/cliniqueduparc.jpg" alt="Clinique du Parc" class="img-responsive img-thumbnail" style="width:100%; height:200px;">
        </div>

        <!-- Image 3 -->
        <div class="col-sm-4">
            <img src="assets/images/hopitalcentral.jpg" alt="Hôpital Central" class="img-responsive img-thumbnail" style="width:100%; height:200px;">
        </div>

        <!-- Image 4 -->
        <div class="col-sm-4">
            <img src="assets/images/lille.jpg" alt="Lille" class="img-responsive img-thumbnail" style="width:100%; height:200px;">
        </div>

        <!-- Image 5 -->
        <div class="col-sm-4">
            <img src="assets/images/roubaix.jpg" alt="Roubaix" class="img-responsive img-thumbnail" style="width:100%; height:200px;">
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>



