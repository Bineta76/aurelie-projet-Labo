<?php include 'includes/header.php';?>
<div class="container">
        

    <h1 class="text-center">Centre d'aide</h1>
    <div class="container">
        <form action="search.php" method="GET">
            <div class="form-group">
                <label for="search">Recherche sur Health North :</label>
                <input type="text" id="search" name="search" class="form-control" required>
            </div>
        </form>

        <center> <h1>Questions fréquentes</h1></center>

        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block w-50" src="assets/images/monEspaceSanté.jpg" alt="Accéder à mon parcours santé">
                    <div class="carousel-caption text-right">
                        <h3>Accéder à mon parcours santé</h3>
                        <h3><p>Voici un lien vers <a href="https://www.monespacesante.fr">le site web</a></p></h3>
                        <h3><p>Activer mon espace santé <a href ="https://www.monespacesante.fr/enrolement/activation/accueil">Accueil de mon espace</a></p></h3>
                        <h3><p><a href ="https://www.monespacesante.fr/enrolement/activation/authentification-date-de-naissance">¨Suivre les étapes</a></p></h3>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="d-block w-50" src="assets/images/pointDinterrogation.jpg" alt="Découvrir Santé">
                    <div class="carousel-caption text-right">
                        <h3>Découvrir Santé</h3>
                       <h3> <p>Un espace pour retrouver tous vos documents et informations de santé</p></h3>
                        <h3><p>Voici un lien vers <a href="https://www.monespacesante.fr/questions-frequentes/gerer-mes-documents-de-sante/1">un site web</a></p></h3>
                        
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="d-block w-50" src="assets/images/images.jpg" alt="J'ai oublié mon mot de passe et mes identifiants">
                    <div class="carousel-caption text-right">
                        <h3>J'ai oublié mon mot de passe et mes identifiants</h3>
                       <h3><p>Voici un lien vers <a href="https://www.monespacesante.fr/questions-frequentes">Questions fréquentes</a></p></h3>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="d-block w-50" src="assets/images/annulerRdsVs.jpg" alt="J'annule mon rendez-vous">
                    <div class="carousel-caption text-right">
                        <h3>J'annule mon rendez-vous</h3>
                        <h3><p>J'annule mon rendez-vous_se déconnecter</p></h3>
                        <h3><p>J'appuie en haut à droite sur mon prénom et je me déconnecte</p></h3>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Précédent</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Suivant</span>
            </a>
        </div>
    </div>
    <?php include 'includes/footer.php';?>
</div>


