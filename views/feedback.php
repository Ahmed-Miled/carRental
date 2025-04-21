<?php
// Démarrer la session EN PREMIER pour lire/modifier les messages flash


require __DIR__ . '/includes/header.php'; // Assurez-vous que le chemin est correct

// --- Déterminer le statut et le message ---
$message = null;
$statusType = 'info'; // Statut par défaut (ni succès, ni erreur)
$specificType = 'generic'; // Type spécifique pour CSS plus fin
$title = 'Information'; // Titre par défaut
$buttonLink = '/carRental/index.php'; // Lien par défaut (accueil)
$buttonText = 'Retour à l\'accueil'; // Texte bouton par défaut
$destroySession = false; // Par défaut, ne pas détruire la session

// Priorité à l'erreur
if (isset($_SESSION['error_message']) || isset($_GET['error'])) {
    $message = $_SESSION['error_message'] ?? $_GET['error'];
    $specificType = $_SESSION['error_type'] ?? $_GET['error_type'] ?? 'generic';
    $statusType = 'error';
    $title = 'Erreur';
    $buttonLink = '/carRental/views/auth/authentification.php'; // Lien spécifique pour erreur (souvent login)
    $buttonText = 'Retour à la page de connexion';
    $destroySession = true; // On garde la logique de l'ancienne page error.php

    // Effacer les messages d'erreur de la session
    unset($_SESSION['error_message']);
    unset($_SESSION['error_type']);

} elseif (isset($_SESSION['success_message']) || isset($_GET['success'])) {
    // Vérifier succès seulement si pas d'erreur
    $message = $_SESSION['success_message'] ?? $_GET['success'];
    $specificType = $_SESSION['success_type'] ?? $_GET['success_type'] ?? 'generic';
    $statusType = 'success';
    $title = 'Succès !';
    // Le lien et le texte par défaut (accueil) conviennent souvent au succès

    // Effacer les messages de succès de la session
    unset($_SESSION['success_message']);
    unset($_SESSION['success_type']);

} else {
    // Aucun message flash trouvé, afficher un message par défaut ou rediriger
    $message = 'Aucune information spécifique à afficher.';
    // Garder statusType = 'info'
}

?>

<style>
    /* --- Styles Combinés --- */

    /* Conteneur générique pour les messages */
    .feedback-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #dee2e6; /* Bordure neutre par défaut */
        border-radius: 5px;
        text-align: center;
        color: #212529; /* Couleur texte par défaut */
        background-color: #f8f9fa; /* Fond par défaut */
    }

    /* Styles spécifiques pour les ERREURS */
    .feedback-container.error {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
    .feedback-container.error h2 {
        color: #721c24;
    }
    /* Styles spécifiques pour les erreurs d'email (hérite de .error) */
    .feedback-container.error.email-error { /* Ciblage plus précis */
        background-color: #fff3cd;
        border-color: #ffeeba;
        color: #856404;
    }
    .feedback-container.error.email-error h2 {
        color: #856404;
    }
    /* Styles spécifiques pour les erreurs d'authentification (hérite de .error) */
     .feedback-container.error.auth-error { /* Ciblage plus précis */
         /* Garde les styles de .error par défaut, pas besoin de répéter sauf si différent */
     }


    /* Styles spécifiques pour les SUCCÈS */
    .feedback-container.success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    .feedback-container.success h2 {
        color: #155724;
    }
     /* Optionnel: type de succès spécifique */
     .feedback-container.success.reservation-success {
        /* border-left: 5px solid #155724; */
     }


    /* Styles spécifiques pour INFO (défaut si ni erreur ni succès) */
    .feedback-container.info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
    .feedback-container.info h2 {
        color: #0c5460;
    }


    /* Styles communs pour titre et message */
    .feedback-container h2 {
        margin-top: 0;
        margin-bottom: 15px; /* Espace sous le titre */
    }

    .feedback-message {
        margin-bottom: 25px; /* Plus d'espace avant le bouton */
        font-size: 1.1em;
        line-height: 1.5; /* Améliore la lisibilité */
    }

    /* Style commun pour le bouton */
    .feedback-redirect-btn {
        display: inline-block;
        padding: 10px 20px;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        border: none; /* Pas de bordure par défaut */
        cursor: pointer;
        transition: background-color 0.3s, opacity 0.3s;
        font-weight: 500;
    }

    /* Couleur du bouton basée sur le statut */
    .feedback-container.error .feedback-redirect-btn {
        background-color: #dc3545; /* Rouge pour erreur */
    }
    .feedback-container.error .feedback-redirect-btn:hover {
        background-color: #c82333;
    }

    .feedback-container.success .feedback-redirect-btn {
        background-color: #28a745; /* Vert pour succès */
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
    <!-- Titre dynamique -->
    <h2><?php echo htmlspecialchars($title); ?></h2>

    <!-- Message dynamique -->
    <p class="feedback-message">
        <?php echo $message; // Ne pas utiliser htmlspecialchars si le message contient du HTML (ex: <br>) venant de votre code contrôlé ?>
        <?php // Si le message vient de l'utilisateur, TOUJOURS utiliser htmlspecialchars() ?>
    </p>

    <!-- Bouton dynamique -->
    <?php if ($message): // N'affiche le bouton que si un message est défini ?>
        <a href="<?php echo htmlspecialchars($buttonLink); ?>" class="feedback-redirect-btn">
            <?php echo htmlspecialchars($buttonText); ?>
        </a>
    <?php endif; ?>
</div>

<?php
// Détruire la session SEULEMENT si une erreur a été affichée
if ($destroySession && $statusType === 'error') {
    $_SESSION = array(); // Efface toutes les variables de session
    // Si vous utilisez des cookies de session, il est bon de le supprimer aussi
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy(); // Détruit la session
}

?>