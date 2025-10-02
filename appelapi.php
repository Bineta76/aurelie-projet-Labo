<?php
// URL de l'API
$url = "http://localhost/api";

// Faire la requête GET
$response = file_get_contents($url);

// Convertir le JSON en tableau PHP
$data = json_decode($response, true);

// Afficher le résultat
print_r($data);
?>
