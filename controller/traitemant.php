<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// 1. CONNEXION DB
$db_path = __DIR__ . '/../config/database.php';
echo "<pre>Chemin DB testé: " . realpath($db_path) . "</pre>";

if (!file_exists($db_path)) {
    die("ERREUR: Fichier non trouvé. Essayer: " . __DIR__ . '/../../config/database.php');
}

require_once $db_path;


// Debug initial
echo "<pre>SERVER: ";
print_r([
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
    'POST_DATA' => $_POST,
    'SESSION' => $_SESSION ?? null
]);
echo "</pre>";



// 2. Vérification PDO
if (!isset($pdo)) {
    die("ERREUR: Connexion DB échouée. Vérifiez database.php");
}

// 3. SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4. TRAITEMENT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>POST reçu:</pre>";
    
    // Force le traitement même sans action=login
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? '';
    
    // Debug avant traitement
    error_log("Tentative de connexion: $email / $role");
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND type = ?");
        $stmt->execute([$email, $role]);
        
        if ($user = $stmt->fetch()) {
            if ($password === $user['password']) {
                $_SESSION['user'] = $user;
                header("Location: /carRental/views/auth/success.php");
                exit();
            } else {
                $_SESSION['error'] = "Mot de passe incorrect";
            }
        } else {
            $_SESSION['error'] = "Compte introuvable";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur DB: " . $e->getMessage();
    }
    
    header("Location: /carRental/views/auth/auth.php");
    exit();
}

// Si on arrive ici, afficher le debug
die("FIN DU SCRIPT - Aucune action traitée");

?>