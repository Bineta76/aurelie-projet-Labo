<?php
include 'includes/header.php';
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Comptes Rendus Médicaux</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .btn-delete:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

<h2>Liste des Comptes Rendus Médicaux</h2>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Médecin Rencontré</th>
            <th>Accéder au Compte Rendu</th>
            <th>Prescriptions</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>2025-04-23</td>
            <td>Dr. Lepic</td>
            <td> J'ai rencontré le patient Madame Binet ,du à une douleur musculaire au niveau de la cheville </td>
            <td>Doliprane et pommade</td>
            <td><button class="btn-delete" onclick="confirmDelete(1)">Supprimer</button></td>
            
        </tr>
        <tr>
            <td>2025-04-22</td>
            <td>Dr. Lepic</td>
            <td>J'ai vu Mme Dupont pour une douleur au pied suite à un faux mouvement</td>
            <td>Radiographie et pommade</td>
            <td><button class="btn-delete" onclick="confirmDelete(2)">Supprimer</button></td>
            
        </tr>
        <tr>
            <td>2025-04-21</td>
            <td>Dr. Lafarge</td>
            <td>J'ai vu Mme Lahalle aux urgences suite à une mauvaise chute dans sa maison,avec perte de connaissance</td>
            <td>IRM et séjour à l'hoptital pour plus d'examens</td>
            <td><button class="btn-delete" onclick="confirmDelete(2)">Supprimer</button></td>
        </tr>
        <tr>
            <td>2025-04-21</td>
            <td>Dr. Lafarge</td>
            <td>J'ai vu Mme Loute aux urgences suite à des douleurs dentaires </td>
            <td>Amoxicicline et doliprane </td>
            <td><button class="btn-delete" onclick="confirmDelete(2)">Supprimer</button></td>
        </tr>
        <tr>
            <td>2025-04-25</td>
            <td>Dr. Lafarge</td>
            <td>J'ai vu Monsieur Lallier aux urgences suite à une chute à son travail </td>
            <td> IRM et radiographie </td>
            <td><button class="btn-delete" onclick="confirmDelete(2)">Supprimer</button></td>
        </tr>
        