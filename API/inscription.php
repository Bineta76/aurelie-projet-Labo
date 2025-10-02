<?php
// Gestion CORS pour les requêtes préliminaires
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gestion de la requête OPTIONS (pré-vol CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Configuration de la base de données
$host = 'localhost';
$dbname = 'labo';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

// Gestion de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

try {
    // Connexion PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    // Récupération des données JSON
    $input = file_get_contents('php://input');
    
    // Vérifier si des données ont été reçues
    if (empty($input)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Aucune donnée reçue'
        ]);
        exit;
    }
    
    $data = json_decode($input, true);
    
    // Vérifier si le JSON est valide
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'JSON invalide: ' . json_last_error_msg()
        ]);
        exit;
    }
    
    // Validation des données
    if (empty($data['nom']) || empty($data['email'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Nom et email sont requis'
        ]);
        exit;
    }
    
    $nom = trim($data['nom']);
    $prenom = isset($data['prenom']) ? trim($data['prenom']) : '';
    $email = trim($data['email']);
    $numero_de_securite_sociale = isset($data['numero_de_securite_sociale']) ? trim($data['numero_de_securite_sociale']) : '';
    
    // Vérifier si le mot de passe est fourni
    if (empty($data['mot_de_passe'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Le mot de passe est requis'
        ]);
        exit;
    }
    
    $mot_de_passe = password_hash(trim($data['mot_de_passe']), PASSWORD_DEFAULT);
    
    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email invalide'
        ]);
        exit;
    }
    
    // Préparation de la requête INSERT
    $sql = "INSERT INTO patient (nom, prenom, email, numero_de_securite_sociale, mot_de_passe) 
            VALUES (:nom, :prenom, :email, :numero_de_securite_sociale, :mot_de_passe)";
    
    $stmt = $pdo->prepare($sql);
    
    // Exécution avec les paramètres
    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':email' => $email,
        ':numero_de_securite_sociale' => $numero_de_securite_sociale,
        ':mot_de_passe' => $mot_de_passe
    ]);
    
    // Récupération de l'ID inséré
    $userId = $pdo->lastInsertId();
    
    // Réponse succès
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Utilisateur créé avec succès',
        'data' => [
            'id' => (int)$userId,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email
        ]
    ]);
    
} catch (PDOException $e) {
    // Gestion des erreurs spécifiques
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'Cet email existe déjà'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur',
            'error' => $e->getMessage()
        ]);
    }
}
?>