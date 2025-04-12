<?php 
require __DIR__ . '/../includes/header.php';
/*
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /carRental/views/auth/login.php');
    exit();
}

// Database connection (adjust credentials as needed)
require __DIR__ . '/../../config/database.php';
$db = new Database();
$pdo = $db->connect();

// Fetch user information
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch rental history
$rental_stmt = $pdo->prepare("
    SELECT r.*, v.model, v.brand, v.image 
    FROM rentals r
    JOIN vehicles v ON r.vehicle_id = v.id
    WHERE r.user_id = ?
    ORDER BY r.rental_date DESC
");
$rental_stmt->execute([$user_id]);
$rentals = $rental_stmt->fetchAll();
*/
?>

<head>
    <link rel="stylesheet" href="/carRental/assets/css/clientDashBoard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="user-profile">
            <img src="/carRental/assets/img/personne1.png" alt="Profile" class="profile-img">
            <h3><?php echo htmlspecialchars($user['username']); ?></h3>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <nav class="nav-menu">
            <a href="#profile" class="nav-item active"><i class="fas fa-user"></i> My Profile</a>
            <a href="#history" class="nav-item"><i class="fas fa-history"></i> Rental History</a>
            <a href="/carRental/views/auth/logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <a href="#delete-account" class="nav-item delete-btn"><i class="fas fa-trash-alt"></i> Delete Account</a>
        </nav>
    </div>

    <div class="main-content">
        <section id="profile" class="content-section active">
            <h2>My Profile</h2>
            <form action="/carRental/controllers/updateProfile.php" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn update-btn">Update Profile</button>
            </form>
        </section>
        
        <section id="history" class="content-section">
            <h2>Rental History</h2>
            <?php if (empty($rentals)): ?>
                <p class="no-history">You haven't rented any vehicles yet.</p>
            <?php else: ?>
                <div class="rental-history">
                    <?php foreach ($rentals as $rental): ?>
                        <div class="rental-card">
                            <img src="/carRental/assets/uploads/vehicles/<?php echo htmlspecialchars($rental['image']); ?>" alt="<?php echo htmlspecialchars($rental['model']); ?>">
                            <div class="rental-info">
                                <h3><?php echo htmlspecialchars($rental['brand'] . ' ' . $rental['model']); ?></h3>
                                <p><strong>Rental Date:</strong> <?php echo date('M j, Y', strtotime($rental['rental_date'])); ?></p>
                                <p><strong>Return Date:</strong> <?php echo date('M j, Y', strtotime($rental['return_date'])); ?></p>
                                <p><strong>Total Cost:</strong> $<?php echo number_format($rental['total_cost'], 2); ?></p>
                                <p><strong>Status:</strong> <span class="status-<?php echo strtolower($rental['status']); ?>"><?php echo $rental['status']; ?></span></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section id="delete-account" class="content-section">
            <h2>Delete Account</h2>
            <div class="delete-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Warning: This action cannot be undone. All your data will be permanently deleted.</p>
            </div>
            <button id="confirm-delete" class="btn delete-btn">Confirm Account Deletion</button>
            
            <!-- Hidden form for actual deletion -->
            <form id="delete-form" action="/carRental/controllers/deleteAccount.php" method="POST" style="display: none;">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            </form>
        </section>
    </div>
</div>

<script>
    // Tab switching
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                
                // Remove active class from all
                document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                document.querySelectorAll('.content-section').forEach(section => section.classList.remove('active'));
                
                // Add active class to clicked
                this.classList.add('active');
                const target = this.getAttribute('href');
                document.querySelector(target).classList.add('active');
            }
        });
    });

    // Account deletion confirmation
    document.getElementById('confirm-delete').addEventListener('click', function() {
        if (confirm('Are you absolutely sure you want to delete your account? This cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    });
</script>

<?php 
require __DIR__ . '/../includes/footer.php';  
?>