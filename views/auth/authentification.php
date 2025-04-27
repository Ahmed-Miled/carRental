<?php
session_start();
require __DIR__ . '/../includes/header.php';  
// Clear existing session errors if page is refreshed
if (!isset($_GET['action'])) {
    unset($_SESSION['login_errors']);
    unset($_SESSION['login_error']);
}

// Initialize variables
$role = $_GET['role'] ?? null;
$isLogin = !isset($_GET['action']) || $_GET['action'] === 'login';
?>

    <title>Authentification - CarRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/carRental/assets/css/authentification.css">

<body>

<div class="auth-wrapper">
    <div class="auth-container">
        <!-- Role Selection -->
        <div class="role-selection ">
            <h2 class="section-title">Bienvenue à CarRent</h2>
            <p class="section-subtitle">Choisissez votre type de compte</p>
            <div class="role-options">
                <a href="?role=client&action=login" class="role-card">
                    <div class="role-icon client">
                        <i class="fas fa-user-alt"></i>
                    </div>
                    <div class="role-content">
                        <h3>Client</h3>
                        <p>Trouvez et louez votre véhicule idéal</p>
                    </div>
                    <div class="role-hover">
                        <span>Commencer <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
                
                <a href="?role=agency&action=login" class="role-card">
                    <div class="role-icon agency">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="role-content">
                        <h3>Propriétaire de l'agence</h3>
                        <p>Gérez votre flotte et vos réservations</p>
                    </div>
                    <div class="role-hover">
                        <span>Gérer maintenant <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Auth Forms -->
        <?php if ($role): ?>
            <div class="auth-forms">
                <div class="form-header">
                    <img src="/carRental/assets/images/logo.png" class="auth-logo" alt="CarRent Logo">
                    <h2 class="form-title"><?= $role === 'client' ? 'Portail client' : 'Portail de l\'agence' ?></h2>
                </div>

                <!-- Error Messages -->
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="error-message">
                        <?= $_SESSION['login_error'] ?>
                    </div>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['login_errors'])): ?>
                    <?php foreach ($_SESSION['login_errors'] as $error): ?>
                        <div class="error-message">
                            <?= $error ?>
                        </div>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['login_errors']); ?>
                <?php endif; ?>

                <!-- Login Form -->
                <form method="POST" action="/carRental/controller/traitemant.php" class="auth-form <?= $isLogin ? 'active' : 'hidden' ?>">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                    <div class="form-group floating">
                        <input type="email" id="logInEmail" name="logInEmail" required placeholder=" ">
                        <label for="logInEmail">Adresse email</label>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>

                    <div class="form-group floating">
                        <input type="password" id="logInPassword" name="logInPassword" required placeholder=" ">
                        <label for="logInPassword">Mot de passe</label>
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle" aria-label="Show password">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Souviens de moi
                        </label>
                        <a href="#" class="forgot-password">Mot de passe oublié?</a>
                    </div>

                    <button type="submit" class="btn-primary">Se connecter</button>

                    <?php if ($role === 'client'): ?>
                        <p class="auth-switch">
                        Vous n'avez pas de compte ? 
                            <a href="?role=<?= $role ?>&action=signup">Créer un compte</a>
                        </p>
                    <?php endif; ?>
                </form>

                <!-- Signup Form -->
                <form method="POST" action="/carRental/controller/traitemant.php" class="auth-form <?= !$isLogin ? 'active' : 'hidden' ?>">
                    <input type="hidden" name="action" value="signup">
                    <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                    <div class="form-group floating">
                        <input type="text" id="SignUpName" name="SignUpName" required placeholder=" ">
                        <label for="SignUpName">Nom et prénom</label>
                        <i class="fas fa-user input-icon"></i>
                    </div>

                    <div class="form-group floating">
                        <input type="tel" id="SignUpPhoneNumber" name="SignUpPhoneNumber" required placeholder=" ">
                        <label for="SignUpPhoneNumber">Numéro de téléphone</label>
                        <i class="fas fa-phone input-icon"></i>
                    </div>

                    <div class="form-group floating">
                        <input type="email" id="SignUpEmail" name="SignUpEmail" required placeholder=" ">
                        <label for="SignUpEmail">Adresse email</label>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>

                    <div class="form-group floating">
                        <input type="password" id="SignUpPassword" name="SignUpPassword" required placeholder=" ">
                        <label for="SignUpPassword">Créer un mot de passe</label>
                        <i class="fas fa-lock input-icon"></i>
                        
                    </div>

                    <button type="submit" class="btn-primary">Créer un compte</button>

                    <p class="auth-switch">
                    Vous avez déjà un compte ? 
                        <a href="?role=<?= $role ?>&action=login">Se connecter</a>
                    </p>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>


<script>
// Password toggle functionality

document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', () => {
            // Find the sibling input element (adjust selector if HTML structure changes)
            const passwordInput = button.closest('.form-group').querySelector('input[type="password"], input[type="text"]');
            const icon = button.querySelector('i');

            if (passwordInput && icon) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    button.setAttribute('aria-label', 'Hide password');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    button.setAttribute('aria-label', 'Show password');
                }
            }
        });
    });
</script>

</body>
</html>