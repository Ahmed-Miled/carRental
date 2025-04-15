<?php
require __DIR__ . '/includes/header.php';
$errorMessage = $_SESSION['error_message'] ?? $_GET['error'] ?? 'Une erreur inconnue est survenue';
$errorType = $_SESSION['error_type'] ?? $_GET['error_type'] ?? 'generic';
?>

<style>
    .error-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        color: #721c24;
        text-align: center;
    }

    .error-container h2 {
        margin-top: 0;
        color: #721c24;
    }

    .error-message {
        margin-bottom: 20px;
        font-size: 1.1em;
    }

    .login-redirect-btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #dc3545;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .login-redirect-btn:hover {
        background-color: #c82333;
    }

    .error-container.email-error {
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
    }
    
    .error-container.auth-error {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
</style>
<div class="error-container <?php echo htmlspecialchars($errorType); ?>-error">
    <h2>Erreur</h2>
    <p class="error-message">
        <?php echo htmlspecialchars($errorMessage); ?>
    </p>
    
    <a href="/carRental/views/auth/authentification.php" class="login-redirect-btn">
        Retour à la page de connexion
    </a>
</div>
<?php
$_SESSION = array();
session_destroy();

?>