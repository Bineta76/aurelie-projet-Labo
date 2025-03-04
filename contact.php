<?php include 'includes/header.php';?>

<div class="container mt-3">
    <div class="topnav">
        <a href=index.php aria-label="Accueil">Accueil</a>
        <a href="quiSommesNous.php" aria-label="Qui sommes-nous ?">Qui sommes-nous?</a>
        <a class="active" href="contact.php" aria-label="Nous contacter">Gestion des utilisateurs</a>
        <a href="rdv.php" aria-label="Créer un rendez-vous">Créer un rendez-vous</a>
        <a href="planningmedecin.php" aria-label="Voir le planning du médecin">Planning</a>
        <a href="centre.php" aria-label="Voir la liste des centres disponibles">Liste des centres</a>
        <a href="contactSupport.php" aria-label="contactSupport">Aide</a>  
    </div>
<h2>Gestion des utilisateurs</h2>
<table>
  <caption>
    Health North
  </caption>
  <tr>
    <th scope="col">Nom d'utilisateur</th>
    
    
  </tr>
  <tr>
    <th scope="row">admin</th>
    <td>Modifier</td>
    <td>Consulter</td>
    <td>Résultat</td>
  </tr>
  <tr>
    <th scope="row">patient</th>
    
    <td>Consulter</td>
    <td>Résultat</td>
  </tr>
  
  
</table>


    <?php include 'includes/footer.php';?>
</div>