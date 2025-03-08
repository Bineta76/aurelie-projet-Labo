

<?php
session_start();  // Start the session

// Database connection parameters
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "labo";

// Create connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if already logged in
if (isset($_SESSION['id_patient'])) {
    header("Location: index.php");
    exit;
}

$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prevent SQL injection
    $username = $conn->real_escape_string($username);

    // Query to check if the user exists
    $sql = "SELECT * FROM patient WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, get the data
        $user = $result->fetch_assoc();

        // Check password using password_verify()
        if (password_verify($password, $user['password'])) {
            // Successful login, set session variables
            $_SESSION['id_patient'] = $user['id_patient'];
            $_SESSION['username'] = $user['username'];

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Redirect to another page
            header("Location: lien.php");
            exit;
        } else {
            // Incorrect password
            $error_message = "Mot de passe incorrect.";
        }
    } else {
        // User doesn't exist
        $error_message = "Nom d'utilisateur incorrect.";
    }

    // Close the prepared statement
    $stmt->close();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <!-- Add CSS for styling -->
</head>
<body>
    <div class="container w-50">
        <h2 class="text-center">Se connecter</h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur :</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </div>
        </form>
    </div>
</body>
</html>
