<?php
include 'includes/header.php';
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendrier</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <style>
    #calendar {
      max-width: 900px;
      margin: 40px auto;
    }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const calendarEl = document.getElementById('calendar')
      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth', // Vue en mois
        locale: 'fr', // Langue en français
        events: [
          { title: 'Événement A', start: '2025-04-05' },
          { title: 'Événement B', start: '2025-04-10', end: '2025-04-12' }
        ]
      })
      calendar.render()
    })
  </script>
</head>
<body>
  <div id="calendar"></div>
</body>
</html>