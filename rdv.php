<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre un rendez-vous</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container">
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
                    <li><a href="inscription.php">Inscription</a></li>
                    <li><a href="quiSommesNous.php">Qui sommes-nous?</a></li>
                    <li><a href="dossierpatient.php">Dossier patient</a></li>
                    <li><a href="dossiermedical.php">Dossier médical</a></li>
                    <li class="active"><a href="rdv.php">Créer un rendez-vous</a></li>
                    <li><a href="historiqueRdv.php">Historique Rendez-vous</a></li>
                    <li><a href="planningmedecin.php">Planning</a></li>
                    <li><a href="centre.php">Liste des centres</a></li>
                    <li><a href="contactSupport.php">Aide</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Prendre un rendez-vous</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label for="centre">Cabinet médical :</label>
                <select name="centre" id="centre" class="form-control" required>
                    <option value="">Sélectionnez un cabinet médical</option>
                    <?php foreach ($cabinetsMedical as $cabinet): ?>
                        <option value="<?php echo htmlspecialchars($cabinet['id_cabinetMedical']); ?>">
                            <?php echo htmlspecialchars($cabinet['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="medecin">Médecin :</label>
                <select name="medecin" id="medecin" class="form-control" required>
                    <option value="">Sélectionnez un médecin</option>
                    <?php foreach ($medecins as $medecin): ?>
                        <option value="<?php echo htmlspecialchars($medecin['id_medecin']); ?>">
                            <?php echo htmlspecialchars($medecin['nom'] . ' ' . $medecin['prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date">Date du rendez-vous :</label>
                <input type="datetime-local" name="date" id="date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="type_examen">Type d'examen :</label>
                <select name="type_examen" id="type_examen" class="form-control" required>
                    <option value="">Sélectionnez un type d'examen</option>
                    <?php foreach ($examens as $examen): ?>
                        <option value="<?php echo htmlspecialchars($examen['id_examen']); ?>">
                            <?php echo htmlspecialchars($examen['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Prendre rendez-vous</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</body>
</html>
