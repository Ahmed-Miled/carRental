<?php
session_start();
require __DIR__ . '/../includes/header.php';

// Clear existing session errors if page is refreshed without form submission context
// This check is simple; more robust checks might involve checking request methods
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    unset($_SESSION['login_errors']);
    unset($_SESSION['login_error']);
}

$role = $_GET['role'] ?? null;
$isLogin = (!isset($_GET['action']) || $_GET['action'] === 'login');


?>

    <title>Authentication - CarRent</title>
    <!-- Ensure Font Awesome is loaded (can be in header.php) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/carRental/assets/css/authentification.css">

<body>

<div class="auth-wrapper">
    <div class="auth-container">

        <!-- Role Selection Section -->
        <!-- Show this section if 'role' is NOT set in the URL -->
        <div class="role-selection <?= $role ? 'hidden' : '' ?>">
            <h2 class="section-title">Welcome to CarRent</h2>
            <p class="section-subtitle">Please select your account type to continue.</p>
            <div class="role-options">
                <!-- Client Role Card -->
                <a href="?role=client&action=login" class="role-card" aria-label="Select Client Role">
                    <div class="role-icon client">
                        <i class="fas fa-user-alt" aria-hidden="true"></i>
                    </div>
                    <div class="role-content">
                        <h3>Client</h3>
                        <p>Find and rent your perfect vehicle with ease.</p>
                    </div>
                    <div class="role-hover" aria-hidden="true">
                        <span>Get Started <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>

                <!-- Agency Role Card -->
                <a href="?role=agency&action=login" class="role-card" aria-label="Select Agency Owner Role">
                    <div class="role-icon agency">
                        <i class="fas fa-building" aria-hidden="true"></i>
                    </div>
                    <div class="role-content">
                        <h3>Agency Owner</h3>
                        <p>Manage your vehicle fleet and reservations efficiently.</p>
                    </div>
                    <div class="role-hover" aria-hidden="true">
                        <span>Manage Now <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Authentication Forms Section -->
        <!-- Show this section if 'role' IS set in the URL -->
        <?php if ($role): ?>
            <div class="auth-forms">
                <div class="form-header">
                    <!-- Make sure logo path is correct -->
                    <img src="/carRental/assets/images/logo.png" class="auth-logo" alt="CarRent Logo">
                    <!-- Use htmlspecialchars for dynamic output -->
                    <h2 class="form-title"><?= htmlspecialchars(ucfirst($role)) ?> Portal</h2>
                    <p class="section-subtitle">
                        <?= $isLogin ? 'Sign in to access your account.' : 'Create your new account.' ?>
                    </p>
                </div>

                <!-- Display General Error Messages (if any) -->
                <?php if (isset($_SESSION['general_error'])): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i> <!-- Example Icon -->
                        <?= htmlspecialchars($_SESSION['general_error']) ?>
                    </div>
                    <?php unset($_SESSION['general_error']); ?>
                <?php endif; ?>

                <!-- Display Specific Login/Signup Error Messages -->
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="error-message">
                         <?= htmlspecialchars($_SESSION['login_error']) ?>
                    </div>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['login_errors'])): // Check if array is not empty ?>
                    <?php foreach ($_SESSION['login_errors'] as $error): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['login_errors']); ?>
                <?php endif; ?>


                <!-- Login Form -->
                <form method="POST" action="/carRental/controller/traitemant.php" class="auth-form <?= $isLogin ? 'active' : 'hidden' ?>" id="login-form" novalidate>
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                    <div class="form-group floating">
                        <input type="email" id="logInEmail" name="logInEmail" required placeholder=" " autocomplete="email">
                        <label for="logInEmail">Email Address</label>
                        <i class="fas fa-envelope input-icon" aria-hidden="true"></i>
                    </div>

                    <div class="form-group floating">
                        <input type="password" id="logInPassword" name="logInPassword" required placeholder=" " autocomplete="current-password">
                        <label for="logInPassword">Password</label>
                        <i class="fas fa-lock input-icon" aria-hidden="true"></i>
                        <button type="button" class="password-toggle" aria-label="Show password">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span class="checkmark" aria-hidden="true"></span>
                            Remember me
                        </label>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-primary">Sign In</button>

                    <?php if ($role === 'client'): // Only clients can easily switch to sign up ?>
                        <p class="auth-switch">
                            Don't have an account?
                            <a href="?role=<?= htmlspecialchars($role) ?>&action=signup">Create Account</a>
                        </p>
                    <?php else: // Agency might have a different process or just a login ?>
                         <p class="auth-switch">
                            Need access? Contact support. <!-- Example for agency -->
                         </p>
                    <?php endif; ?>
                </form>

                <!-- Signup Form (Only shown for clients in this logic, but structure is here) -->
                <?php if ($role === 'client'): ?>
                    <form method="POST" action="/carRental/controller/traitemant.php" class="auth-form <?= !$isLogin ? 'active' : 'hidden' ?>" id="signup-form" novalidate>
                        <input type="hidden" name="action" value="signup">
                        <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">

                        <div class="form-group floating">
                            <input type="text" id="SignUpName" name="SignUpName" required placeholder=" " autocomplete="name">
                            <label for="SignUpName">Full Name</label>
                            <i class="fas fa-user input-icon" aria-hidden="true"></i>
                        </div>

                        <div class="form-group floating">
                            <input type="tel" id="SignUpPhoneNumber" name="SignUpPhoneNumber" required placeholder=" " autocomplete="tel">
                            <label for="SignUpPhoneNumber">Phone Number</label>
                            <i class="fas fa-phone input-icon" aria-hidden="true"></i>
                        </div>

                        <div class="form-group floating">
                            <input type="email" id="SignUpEmail" name="SignUpEmail" required placeholder=" " autocomplete="email">
                            <label for="SignUpEmail">Email Address</label>
                            <i class="fas fa-envelope input-icon" aria-hidden="true"></i>
                        </div>

                        <div class="form-group floating">
                            <input type="password" id="SignUpPassword" name="SignUpPassword" required placeholder=" " autocomplete="new-password">
                            <label for="SignUpPassword">Create Password</label>
                            <i class="fas fa-lock input-icon" aria-hidden="true"></i>
                            <button type="button" class="password-toggle" aria-label="Show password">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                            <!-- Placeholder for password strength indicator -->
                            <div class="password-strength" aria-live="polite">
                                <span class="strength-bar"></span>
                                <span class="strength-bar"></span>
                                <span class="strength-bar"></span>
                            </div>
                        </div>

                        <button type="submit" class="btn-primary">Create Account</button>

                        <p class="auth-switch">
                            Already have an account?
                            <a href="?role=<?= htmlspecialchars($role) ?>&action=login">Sign In</a>
                        </p>
                    </form>
                <?php endif; ?> <!-- End signup form conditional -->

            </div> <!-- End auth-forms -->
        <?php endif; ?> <!-- End role conditional -->

    </div> <!-- End auth-container -->
</div> <!-- End auth-wrapper -->

<?php
// Assuming footer.php includes closing body/html tags and potentially JS scripts
require __DIR__ . '/../includes/footer.php';
?>

<!-- Inline script for password toggle (or move to a separate JS file linked in footer.php) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Add potential JS for password strength meter or other dynamic behaviours here
});
</script>

<!-- Footer.php might start here if it includes closing tags -->
<?php require __DIR__ . '/../includes/footer.php'; ?>
