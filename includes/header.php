<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>health_north</title>
    <link href="assets/css/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="
https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js
"></script>

    

</head>
<html>
<body>
<div class="container">
    <nav class="topnav" aria-label="Navigation principale">
        <a <?=$_SERVER['PHP_SELF'] == '/index.php' ? ' class="active"' : ''?>href="index.php">Accueil</a>
        <a <?=$_SERVER['PHP_SELF'] == '/inscription.php' ? ' class="active"' : ''?>href="inscription.php">Inscription</a>
        <a <?=$_SERVER['PHP_SELF'] == '/quiSommesNous.php' ? ' class="active"' : ''?>href="quiSommesNous.php">Qui sommes-nous ?</a>
        <a <?=$_SERVER['PHP_SELF'] == '/liste_des_Rdv.php' ? ' class="active"' : ''?>href="liste_des_Rdv.php">liste_des_Rdv</a> 
        <a <?=$_SERVER['PHP_SELF'] == '/dossier_medical.php' ? ' class="active"' : ''?>href="dossier_medical.php">Dossier medical</a> 
        <a <?=$_SERVER['PHP_SELF'] == '/rdv.php' ? ' class="active"' : ''?>href="rdv.php">Créer un rendez-vous</a>
        <a <?=$_SERVER['PHP_SELF'] == '/planningmedecin.php' ? ' class="active"' : ''?>href="planningmedecin.php">Planning</a>
        <a <?=$_SERVER['PHP_SELF'] == '/centre.php' ? ' class="active"' : ''?>href="centre.php">Liste des centres</a>
        <a <?=$_SERVER['PHP_SELF'] == '/contactSupport.php' ? ' class="active"' : ''?>href="contactSupport.php">Aide</a>
    </nav>
</div>