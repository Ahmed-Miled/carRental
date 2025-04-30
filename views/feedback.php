<?php

require __DIR__ . '/includes/header.php'; 

// --- Déterminer le statut et le message ---
$message = null;
$statusType = 'info'; 
$specificType = 'generic'; 
$title = 'Information'; 
$buttonLink = '/carRental/index.php'; 
$buttonText = 'Retour à l\'accueil'; 
$destroySession = false; 

// Priorité à l'erreur
if (isset($_SESSION['error_message']) || isset($_GET['error'])) {
    $message = $_SESSION['error_message'] ?? $_GET['error'];
    $specificType = $_SESSION['error_type'] ?? $_GET['error_type'] ?? 'generic';
    $statusType = 'error';
    $title = 'Erreur';
    $buttonLink = '/carRental/views/auth/authentification.php'; 
    $buttonText = 'Retour à la page de connexion';
    $destroySession = true; 

    
    unset($_SESSION['error_message']);
    unset($_SESSION['error_type']);

} elseif (isset($_SESSION['success_message']) || isset($_GET['success'])) {
    // Vérifier succès seulement si pas d'erreur
    $message = $_SESSION['success_message'] ?? $_GET['success'];
    $specificType = $_SESSION['success_type'] ?? $_GET['success_type'] ?? 'generic';
    $statusType = 'success';
    $title = 'Succès !';
    // Le lien et le texte par défaut (accueil) conviennent souvent au succès

    unset($_SESSION['success_message']);
    unset($_SESSION['success_type']);

} else {
    // Aucun message flash trouvé, afficher un message par défaut ou rediriger
    $message = 'Aucune information spécifique à afficher.';
}

?>

<style>

    /* Conteneur générique pour les messages */
    .feedback-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #dee2e6; 
        border-radius: 5px;
        text-align: center;
        color: #212529;
        background-color: #f8f9fa;
    }

    .feedback-container.error {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    .feedback-container.error h2 {
        color: #721c24;
    }

    .feedback-container.error.email-error { 
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
    }
    .feedback-container.error.email-error h2 {
        color: #856404;
    }
    
    .feedback-container.success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    .feedback-container.success h2 {
        color: #155724;
    }
    
    .feedback-container.success.reservation-success {
        border-left: 5px solid #155724; 
     }

    .feedback-container.info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
    .feedback-container.info h2 {
        color: #0c5460;
    }

    .feedback-container h2 {
        margin-top: 0;
        margin-bottom: 15px; 
    }

    .feedback-message {
        margin-bottom: 25px; 
        font-size: 1.1em;
        line-height: 1.5;
    }

    .feedback-redirect-btn {
        display: inline-block;
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        border: none; 
        cursor: pointer;
        transition: background-color 0.3s, opacity 0.3s;
        font-weight: 500;
    }

    .feedback-container.error .feedback-redirect-btn {
        background-color: #dc3545; 
    }
    .feedback-container.error .feedback-redirect-btn:hover {
        background-color: #c82333;
    }

    .feedback-container.success .feedback-redirect-btn {
        background-color: #28a745; 
    }
    .feedback-container.success .feedback-redirect-btn:hover {
        background-color: #218838;
    }

    .feedback-container.info .feedback-redirect-btn {
        background-color: #007bff; /* Bleu pour info/défaut */
    }
     .feedback-container.info .feedback-redirect-btn:hover {
        background-color: #0056b3;
     }

</style>

<!-- Conteneur principal avec classes dynamiques -->
<div class="feedback-container <?php echo htmlspecialchars($statusType); ?> <?php echo htmlspecialchars($specificType); ?>-<?php echo htmlspecialchars($statusType); // Ajoute ex: 'email-error' ou 'reservation-success' ?>">
    
    <h2><?php echo htmlspecialchars($title); ?></h2>

    <p class="feedback-message">
        <?php echo $message;?>
    </p>

    <?php if ($message): ?>
        <a href="<?php echo htmlspecialchars($buttonLink); ?>" class="feedback-redirect-btn">
            <?php echo htmlspecialchars($buttonText); ?>
        </a>
    <?php endif; ?>
</div>

<?php

if ($destroySession && $statusType === 'error') {
    $_SESSION = array(); // Efface toutes les variables de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy(); 
}

?>