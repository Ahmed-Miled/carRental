<?php
// Démarrer la session pour pouvoir lire les messages flash (si pas déjà fait dans header.php)

require __DIR__ . '/includes/header.php'; // Assurez-vous que le chemin est correct

// Récupérer le message et le type de succès depuis la session ou GET
// Utiliser des clés différentes de celles d'erreur pour éviter les conflits
$successMessage = $_SESSION['success_message'] ?? $_GET['success'] ?? 'Opération effectuée avec succès !';
$successType = $_SESSION['success_type'] ?? $_GET['success_type'] ?? 'generic'; // Type optionnel

// IMPORTANT : Effacer le message de la session après l'avoir lu
// pour qu'il ne s'affiche qu'une seule fois (mécanisme "flash")
unset($_SESSION['success_message']);
unset($_SESSION['success_type']);

?>

<style>
    /* Container principal pour le succès */
    .success-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        /* Couleurs de succès : fond vert clair, bordure/texte vert foncé */
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        color: #155724;
        text-align: center;
    }

    .success-container h2 {
        margin-top: 0;
        color: #155724; /* Couleur titre succès */
    }

    /* Style pour le message de succès */
    .success-message {
        margin-bottom: 20px;
        font-size: 1.1em;
    }

    /* Style pour le bouton de redirection (plus générique) */
    .action-redirect-btn {
        display: inline-block;
        padding: 10px 20px;
        /* Couleur primaire (bleu) ou de succès (vert) */
        background-color: #28a745; /* Vert succès */
        /* background-color: #007bff; */ /* Alternative: Bleu primaire */
        color: white;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .action-redirect-btn:hover {
        background-color: #218838; /* Vert plus foncé au survol */
        /* background-color: #0056b3; */ /* Alternative: Bleu plus foncé */
    }

    /* Optionnel : Styles spécifiques basés sur $successType */
    /* Exemple : Pourrait être utilisé pour une icône ou une couleur différente */
    .success-container.reservation-success {
        /* border-left: 5px solid #155724; */ /* Exemple d'accentuation */
    }
    /* Ajoutez d'autres types si nécessaire */

</style>

<!-- Ajoute la classe dynamique basée sur $successType -->
<div class="success-container <?php echo htmlspecialchars($successType); ?>-success">
    <h2>Succès !</h2> <!-- Titre indiquant le succès -->
    <p class="success-message">
        <?php echo htmlspecialchars($successMessage); ?> <!-- Affichage du message -->
    </p>

    <!-- Bouton d'action : lien vers l'accueil ou le tableau de bord -->
    <a href="/carRental/index.php" class="action-redirect-btn">
        Retour à l'accueil
    </a>
    <!-- Alternative: Lien vers le tableau de bord client si pertinent -->
    <!-- <a href="/carRental/views/dashboard/clientDashboard.php" class="action-redirect-btn">
        Aller à mon tableau de bord
    </a> -->
</div>

<?php
// Normalement, NE PAS détruire la session ici,
// sauf si le succès marque la fin absolue d'une interaction (ex: déconnexion réussie)
// $_SESSION = array();
// session_destroy();

// Inclure le footer si vous en avez un
 require __DIR__ . '/includes/footer.php'; // Assurez-vous que le chemin est correct
?>