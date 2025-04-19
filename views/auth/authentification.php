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
            <h2 class="section-title">Welcome to CarRent</h2>
            <p class="section-subtitle">Choose your account type</p>
            <div class="role-options">
                <a href="?role=client&action=login" class="role-card">
                    <div class="role-icon client">
                        <i class="fas fa-user-alt"></i>
                    </div>
                    <div class="role-content">
                        <h3>Client</h3>
                        <p>Find and rent your perfect vehicle</p>
                    </div>
                    <div class="role-hover">
                        <span>Get Started <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
                
                <a href="?role=agency&action=login" class="role-card">
                    <div class="role-icon agency">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="role-content">
                        <h3>Agency Owner</h3>
                        <p>Manage your fleet and reservations</p>
                    </div>
                    <div class="role-hover">
                        <span>Manage Now <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Auth Forms -->
        <?php if ($role): ?>
            <div class="auth-forms">
                <div class="form-header">
                    <img src="/carRental/assets/images/logo.png" class="auth-logo" alt="CarRent Logo">
                    <h2 class="form-title"><?= $role === 'client' ? 'Client Portal' : 'Agency Portal' ?></h2>
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
                        <label for="logInEmail">Email Address</label>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>

                    <div class="form-group floating">
                        <input type="password" id="logInPassword" name="logInPassword" required placeholder=" ">
                        <label for="logInPassword">Password</label>
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle" aria-label="Show password">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-primary">Sign In</button>

                    <?php if ($role === 'client'): ?>
                        <p class="auth-switch">
                            Don't have an account? 
                            <a href="?role=<?= $role ?>&action=signup">Create Account</a>
                        </p>
                    <?php endif; ?>
                </form>

                <!-- Signup Form -->
                <form method="POST" action="/carRental/controller/traitemant.php" class="auth-form <?= !$isLogin ? 'active' : 'hidden' ?>">
                    <input type="hidden" name="action" value="signup">
                    <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                    <div class="form-group floating">
                        <input type="text" id="SignUpName" name="SignUpName" required placeholder=" ">
                        <label for="SignUpName">Full Name</label>
                        <i class="fas fa-user input-icon"></i>
                    </div>

                    <div class="form-group floating">
                        <input type="tel" id="SignUpPhoneNumber" name="SignUpPhoneNumber" required placeholder=" ">
                        <label for="SignUpPhoneNumber">Phone Number</label>
                        <i class="fas fa-phone input-icon"></i>
                    </div>

                    <div class="form-group floating">
                        <input type="email" id="SignUpEmail" name="SignUpEmail" required placeholder=" ">
                        <label for="SignUpEmail">Email Address</label>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>

                    <div class="form-group floating">
                        <input type="password" id="SignUpPassword" name="SignUpPassword" required placeholder=" ">
                        <label for="SignUpPassword">Create Password</label>
                        <i class="fas fa-lock input-icon"></i>
                        
                    </div>

                    <button type="submit" class="btn-primary">Create Account</button>

                    <p class="auth-switch">
                        Already have an account? 
                        <a href="?role=<?= $role ?>&action=login">Sign In</a>
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