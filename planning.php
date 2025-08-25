<?php
include 'includes/header.php';
session_start();


// Connexion à la base MySQL
try {
    $pdo = new PDO("mysql:host=localhost;dbname=labo;charset=utf8", "root", ""); // adapte login/mdp
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}

// Gestion AJAX pour ajout / récupération
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json'); // Important : en-tête JSON

    if($_POST['action'] === 'add') {
        $patient = $_POST['patient'] ?? '';
        $rdv_datetime = $_POST['rdv_datetime'] ?? '';
        if($patient && $rdv_datetime) {
            // Convertir T en espace pour MySQL
            $rdv_datetime = str_replace("T", " ", $rdv_datetime);
            $stmt = $pdo->prepare("INSERT INTO rendez_vous (patient, rdv_datetime) VALUES (?, ?)");
            $stmt->execute([$patient, $rdv_datetime]);
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false]);
            exit;
        }
    }

    if($_POST['action'] === 'get') {
        $stmt = $pdo->query("SELECT * FROM rendez_vous");
        $rdvs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rdvs);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Calendrier Médecin - Prise de RDV</title>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    #calendar { max-width: 900px; margin: 0 auto; }
    #rdvForm {
      display: none;
      position: fixed;
      top: 20%;
      left: 50%;
      transform: translateX(-50%);
      background: #f0f0f0;
      padding: 20px;
      border: 1px solid #aaa;
      box-shadow: 0 0 10px #999;
      z-index: 1000;
    }
    #rdvForm input, #rdvForm button {
      margin-top: 10px;
      width: 100%;
      padding: 8px;
    }
    #overlay {
      display: none;
      position: fixed;
      top:0; left:0; right:0; bottom:0;
      background: rgba(0,0,0,0.4);
      z-index: 900;
    }
  </style>
</head>
<body>

<h1>Calendrier Médecin - Prise de Rendez-vous</h1>
<div id="calendar"></div>

<div id="overlay"></div>

<div id="rdvForm">
  <h3>Nouvel RDV</h3>
  <form id="formRdv">
    <label>Patient :<br><input type="text" id="patientName" required></label><br>
    <label>Date & heure :<br><input type="datetime-local" id="rdvDateTime" required></label><br>
    <button type="submit">Ajouter RDV</button>
    <button type="button" id="cancelBtn">Annuler</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');
  const form = document.getElementById('formRdv');
  const rdvForm = document.getElementById('rdvForm');
  const overlay = document.getElementById('overlay');
  const cancelBtn = document.getElementById('cancelBtn');

  let calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    selectable: true,
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    dateClick: function(info) {
      openForm(info.dateStr + "T09:00");
    },
    eventClick: function(info) {
      if(confirm(`Supprimer le rendez-vous de "${info.event.title}" le ${info.event.start.toLocaleString()} ?`)) {
        // Ici on pourrait ajouter suppression serveur
        info.event.remove();
      }
    },
    events: function(fetchInfo, successCallback, failureCallback) {
      const formData = new URLSearchParams();
      formData.append('action', 'get');

      fetch('calendrier.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: formData.toString()
      })
      .then(res => res.json())
      .then(data => {
        const events = data.map(rdv => ({
          title: rdv.patient,
          start: rdv.rdv_datetime,
          allDay: false
        }));
        successCallback(events);
      })
      .catch(err => failureCallback(err));
    }
  });

  calendar.render();

  function openForm(defaultDateTime) {
    document.getElementById('patientName').value = '';
    document.getElementById('rdvDateTime').value = defaultDateTime;
    rdvForm.style.display = 'block';
    overlay.style.display = 'block';
  }

  function closeForm() {
    rdvForm.style.display = 'none';
    overlay.style.display = 'none';
  }

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const patient = document.getElementById('patientName').value.trim();
    const dateTime = document.getElementById('rdvDateTime').value;

    if(patient && dateTime) {
      const formData = new URLSearchParams();
      formData.append('action', 'add');
      formData.append('patient', patient);
      formData.append('rdv_datetime', dateTime);

      fetch('calendrier.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: formData.toString()
      })
      .then(res => res.json())
      .then(data => {
        if(data.success) {
          calendar.addEvent({title: patient, start: dateTime, allDay: false});
          closeForm();
        } else {
          alert("Erreur lors de l'ajout du rendez-vous !");
        }
      })
      .catch(err => alert("Erreur serveur : " + err));
    } else {
      alert("Veuillez remplir tous les champs !");
    }
  });

  cancelBtn.addEventListener('click', closeForm);
  overlay.addEventListener('click', closeForm);
});
</script>

</body>
</html>
