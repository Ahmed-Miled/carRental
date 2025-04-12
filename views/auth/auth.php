


<?php 
// Afficher les erreurs
if (isset($_SESSION['login_error'])) {
    echo '<div class="error-message">'.$_SESSION['login_error'].'</div>';
    unset($_SESSION['login_error']);
}

if (isset($_SESSION['login_errors'])) {
    foreach ($_SESSION['login_errors'] as $error) {
        echo '<div class="error-message">'.$error.'</div>';
    }
    unset($_SESSION['login_errors']);
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
require __DIR__ . '/../includes/header.php';  

$role = $_GET['role'] ?? null; // Gets ?role=client or ?role=agency
$isLogin = !isset($_GET['action']) || $_GET['action'] === 'login';
require_once __DIR__ . '/../../config/database.php';


?>

<head>
  <title>Authentification - CarRent</title>
  <link rel="stylesheet" href="/carRental/assets/css/auth.css">
</head>
<body>

  <div class="auth-container">
    <!-- Role Selection (shown first) -->
    <div class="role-selection <?= $role ? 'hidden' : '' ?>">
      <h2>Choisissez votre profil</h2>
      <div class="role-options">
        <a href="?role=client&action=login" class="role-card">
          <div class="role-icon">👤</div>
          <h3>Client</h3>
          <p>Louez des véhicules</p>
        </a>
        
        <a href="?role=agency&action=login" class="role-card">
          <div class="role-icon">🏢</div>
          <h3>Propriétaire</h3>
          <p>Gérez votre agence</p>
        </a>
      </div>
    </div>

    <!-- Auth Forms (shown after role selection) -->
    <?php if ($role): ?>
      <div class="auth-forms">
        <!-- Login Form --> 
        <form method="POST" action="/carRental/controller/trait.php" class="auth-form <?= $isLogin ? '' : 'hidden' ?>">
          <input type="hidden" name="action" value="login">
          <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">
          
          <h2>Connexion <?= $role === 'client' ? 'Client' : 'Propriétaire' ?></h2>
          
          <div class="form-group">
            <label for="logInEmail">Email</label>
            <input type="email" id="logInEmail" name="logInEmail" required>
          </div>
          
          <div class="form-group">
            <label for="logInPassword">Mot de passe</label>
            <input type="password" id="logInPassword" name="logInPassword" required>
          </div>
    <!-- name="action" -->
          <button type="submit"  class="btn-primary">Se connecter</button>
          <?php if ($role === 'client'): ?>
          
          <p class="auth-switch">
            Pas de compte? 
            <a href="?role=<?= $role ?>&action=signup">Créer un compte</a>
          </p>
          <?php endif; /*le proprietere d'agence doit cree le compte en demande de l'admin du site */ ?>
        </form>




        <form method="POST" action="/carRental/controller/trait.php" class="auth-form <?= !$isLogin ? '' : 'hidden' ?>">
          <input type="hidden" name="action" value="signup">
          <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">
          
          <h2>Inscription <?= $role === 'client' ? 'Client' : 'Propriétaire' ?></h2>
          
          <div class="form-group">
            <label for="SignUpName">Nom complet</label>
            <input type="text" id="SignUpName" name="SignUpName" required>
          </div>
          
          <div class="form-group">
            <label for="SignUpPhoneNumber">Phone number</label>
            <input type="text" id="SignUpPhoneNumber" name="SignUpPhoneNumber" required>
          </div>

          <div class="form-group">
            <label for="SignUpEmail">Email</label>
            <input type="email" id="SignUpEmail" name="SignUpEmail" required>
          </div>
          
          <div class="form-group">
            <label for="SignUpPassword">Mot de passe</label>
            <input type="password" id="SignUpPassword" name="SignUpPassword" required>
          </div>
          
          <button type="submit" class="btn-primary">S'inscrire</button>
          
          <p class="auth-switch">
            Déjà un compte? 
            <a href="?role=<?= $role ?>&action=login">Se connecter</a>
          </p>        
        </form>
      </div>
    <?php endif; ?>
  </div>

  <?php require __DIR__ . '/../includes/footer.php'; ?>