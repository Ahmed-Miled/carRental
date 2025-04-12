<?php
require __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: /carRental/views/auth/auth.php");
    exit();
}
echo "<script>console.log('redirected to success bien terminé');</script>";
?>

<div class="success-container">
    <h2>Connexion réussie !</h2>
    <p>Bienvenue, <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
    <p>Vous êtes connecté en tant que <?= $_SESSION['user']['type'] === 'client' ? 'Client' : 'Propriétaire' ?></p>
    
    <div class="actions">
        <a href="/carRental/" class="btn">Accueil</a>
        <a href="/carRental/controller/logout.php" class="btn">Déconnexion</a>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>