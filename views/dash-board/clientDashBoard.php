<?php 

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../../config/database.php';


// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: /carRental/views/auth/logout.php');
    exit();
}
/*
// Database connection
require __DIR__ . '/../../config/database.php';
$db = new Database();
$pdo = $db->connect();

// Fetch user information
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();



function getUserId($pdo, $email){
    try{
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['id'];
    }catch (Exception $e){
        "<script> console.log(" . json_encode($e) . ");</script>";
        return false;
    }
}*/
// Fetch rental history
$rental_stmt = $pdo->prepare("
    SELECT * FROM reservations 
    WHERE user_id = ?;
");
$i=1;
$rental_stmt->execute([$i]);
$rentals = $rental_stmt->fetchAll();
echo "<script>console.log('connected');</script>";
?>

<link rel="stylesheet" href="/carRental/assets/css/dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="dashboard-container">
    <!-- Removed the sidebar since we have the main nav -->
    
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
            <div class="dashboard-actions">
                <a href="/carRental/views/auth/logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Profile Section -->
            <div class="dashboard-card">
                <h2><i class="fas fa-user-circle"></i> Profile Information</h2>
                <form action="/carRental/controller/updateProfile.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" 
                               value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>">
                    </div><!--
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" 
                               value="<?php /*echo htmlspecialchars($_SESSION['user_email']); */ ?>">
                    </div>-->
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" 
                               value="<?php echo htmlspecialchars($_SESSION['phoneNumber'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- Rental History -->
            <div class="dashboard-card">
                <h2><i class="fas fa-history"></i> Rental History</h2>
                <?php if (empty($rentals)): ?>
                    <div class="alert alert-info">No rental history found.</div>
                <?php else: ?>
                    <div class="rental-list">
                        <?php foreach ($rentals as $rental): ?>
                            <div class="rental-item">
                                <img src="/carRental/assets/uploads/vehicles/<?php echo htmlspecialchars($rental['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($rental['brand'].' '.$rental['model']); ?>">
                                <div class="rental-details">
                                    <h4><?php echo htmlspecialchars($rental['brand'].' '.$rental['model']); ?></h4>
                                    <div class="rental-meta">
                                        <span><i class="fas fa-calendar-alt"></i> <?php echo $rental['start_date']; ?></span>
                                        <span><i class="fas fa-dollar-sign"></i> <?php echo number_format($rental['total_cost'], 2); ?></span>
                                        <span class="badge bg-<?php 
                                            echo $rental['status'] === 'completed' ? 'success' : 
                                                 ($rental['status'] === 'pending' ? 'warning' : 'danger'); 
                                        ?>">
                                            <?php echo ucfirst($rental['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Account Management -->
            <div class="dashboard-card danger-zone">
                <h2><i id="warning-icon" class="fas fa-exclamation-triangle"></i> Account Management</h2>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> These actions are irreversible.
                </div>
                
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt"></i> Delete My Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Account Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>This will permanently delete your account and all associated data. This action cannot be undone.</p>
                <form id="deleteForm" action="/carRental/controllers/deleteAccount.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" name="password" required>
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="deleteForm" class="btn btn-danger">Delete Account</button>
            </div>
        </div>
    </div>
</div>

<?php 
require __DIR__ . '/../includes/footer.php';  
?>