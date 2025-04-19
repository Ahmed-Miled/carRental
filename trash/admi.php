<?php
/*
session_start();
$_SESSION = array();
session_destroy();

// Redirect to login page
header("Location: /carRental/index.php");
exit();
*/
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//session_start();
//require __DIR__ . '/../../config/database.php';
//include '/carRental/controller/process_messages.php';
//require_once __DIR__ . '/../controller/process_messages.php';
//require_once ROOT_PATH . 'controller/process_messages.php';
require_once __DIR__ . '/../../config/paths.php';
require_once ROOT_DIR . '/config/database.php';
require_once ROOT_DIR . '/controller/process_messages.php';

$messages = getMessages($pdo);
echo "<script>console.log('messages: " . json_encode($messages) . "');</script>";
/*
// Check admin authentication
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /carRental/index.php');
    exit();
}
*/
// Fetch data from database
try {
    // Get all clients
    $clientStmt = $pdo->prepare("SELECT * FROM users WHERE type = 'client'");
    $clientStmt->execute();
    $clients = $clientStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all agencies
    $agencyStmt = $pdo->prepare("SELECT * FROM users WHERE type = 'agency'");
    $agencyStmt->execute();
    $agencies = $agencyStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all cars with agency info
    $carStmt = $pdo->prepare("
        SELECT cars.*, users.name AS agency_name 
        FROM cars 
        JOIN users ON cars.agency_id = users.id
    ");
    $carStmt->execute();
    $cars = $carStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}


// Get active bookings count
$activeBookingsStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reservations 
    WHERE status = 'active' 
      AND end_date > NOW()
");
$activeBookingsStmt->execute();
$activeBookings = $activeBookingsStmt->fetchColumn();

// Get truly available cars count
try {
    $availableCarsStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM cars 
        WHERE status = 'available' 
        AND id NOT IN (
            SELECT car_id 
            FROM reservations 
            WHERE status = 'active' 
            AND NOW() BETWEEN start_date AND end_date
        )
    ");
    $availableCarsStmt->execute();
    $availableCarsCount = $availableCarsStmt->fetchColumn();
} catch (PDOException $e) {
    $availableCarsCount = 0; // Fallback value
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="/carRental/assets/css/admin.css">

</head>

<body>
    <div class="admin-container">
        <!-- Left Navigation (keep existing) -->
        <nav class="admin-sidebar">
            <h2 class="admin-title">Admin Panel</h2>
            <ul class="admin-nav">
                <li class="nav-item active" data-target="status"><a href="#">Status</a></li>
                <li class="nav-item" data-target="users"><a href="#">Users</a></li>
                <li class="nav-item" data-target="agencies"><a href="#">Agencies</a></li>
                <li class="nav-item" data-target="cars"><a href="#">Available Cars</a></li>
                <li class="nav-item" data-target="messages"><a href="#">Messages</a></li>
                <li class="nav-item"><a href="/carRental/views/auth/logout.php">Log Out</a></li>
            </ul>
        </nav>
        <!-- Main Content -->
        <main class="admin-main">

            <section class="content-section active" id="status">
                <div class="section-header">
                    <h3>Quick Status Overview</h3>
                </div>

                <div class="status-cards">
                    <!-- Total Users Card -->
                    <div class="status-card">
                        <div class="card-icon">👥</div>
                        <div class="card-content">
                            <h4>Total Users</h4>
                            <p><?= count($clients) + count($agencies) ?></p>
                        </div>
                    </div>

                    <!-- Active Bookings Card -->
                    <div class="status-card">
                        <div class="card-icon">🚗</div>
                        <div class="card-content">
                            <h4>Active Bookings</h4>
                            <p><?= $activeBookings ?> Ongoing</p>
                        </div>
                    </div>
                    

                    <!-- Recent Activity Card -->
                    <div class="status-card">
                        <div class="card-icon">📅</div>
                        <div class="card-content">
                            <h4>Recent Activity</h4>
                            <ul class="activity-list">
                                <li><?= count($cars) ?> cars added</li>
                                <li><?=  count($messages) ?> messages received</li>
                                <li>2 new signups</li>
                            </ul>
                        </div>
                    </div>

                    <!-- System Status Card -->
                    <div class="status-card">
                        <div class="card-icon">✅</div>
                        <div class="card-content">
                            <h4>System Status</h4>
                            <p class="text-success">All Systems Operational</p>
                        </div>
                    </div>
                </div>
                
                <!-- Simple Statistics -->
                <div class="quick-stats">
                    <div class="stat-item">
                        <h5>Daily Signups</h5>
                        <p>0 users today</p>
                    </div>
                    <div class="stat-item">
                        <h5>Messages</h5>
                        <p><?= count($messages) ?> unread</p>
                    </div>
                    <div class="stat-item">
                        <h5>Available Cars</h5>
                        <p><?= $availableCarsCount ?> listed</p>
                    </div>
                </div>
                
            </section>


            <!-- Users Section -->
            <section class="content-section" id="users">
                <div class="section-header">
                    <div class="header-group">
                        <h3>Manage Users</h3>
                    </div>
                    <div class="section-actions">
                        <span class="stat-badge new-messages">
                            <?= count($clients) ?> Total
                        </span>
                    </div>
                </div>
                <div class="users-list">
                    <?php foreach ($clients as $user): ?>
                    <div class="user-card">
                        <div class="user-info">
                            <h4><?= htmlspecialchars($user['name']) ?></h4>
                            <p><?= htmlspecialchars($user['email']) ?></p>
                            <p><?= htmlspecialchars($user['phoneNumber']) ?></p>
                        </div>
                        <div class="user-actions">
                            <button class="btn-view" 
                                onclick="window.location.href='clientDashBoard.php?user_id=<?= $user['id'] ?>'">
                                View Dashboard
                            </button>
                            <form action="/carRental/views/includes/delete.php" method="POST" style="display: inline;">
                                <input type="hidden" name="type" value="user">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Agencies Section -->
            <section class="content-section" id="agencies">
                <div class="section-header">
                    <div class="header-group">
                        <h3>Manage Agencies</h3>
                        <div class="section-actions">
                        <span class="stat-badge new-messages">
                            <?= count($agencies) ?> Total
                        </span>
                    </div>
                    </div>
                    <button class="btn-add">+ Add Agency</button>
                </div>

                <!-- Add Agency Form -->
                <form class="add-agency-form" action="addAgency.php" method="POST">
                    <input type="text" name="name" placeholder="Agency Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="tel" name="phone" placeholder="Phone Number" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel">Cancel</button>
                        <button type="submit" class="btn-save">Save Agency</button>
                    </div>
                </form>
            
                <!--Agencies List -->
                <div class="agencies-list">
                    <?php foreach ($agencies as $agency): ?>
                    <div class="user-card">
                        <div class="user-info">
                            <h4><?= htmlspecialchars($agency['name']) ?></h4>
                            <p><?= htmlspecialchars($agency['email']) ?></p>
                            <p><?= htmlspecialchars($agency['phoneNumber']) ?></p>
                        </div>
                        <div class="user-actions">
                            <form action="/carRental/views/includes/delete.php" method="POST">
                                <input type="hidden" name="type" value="agency">
                                <input type="hidden" name="id" value="<?= $agency['id'] ?>">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </section>

            <!-- Cars Section -->
            <section class="content-section" id="cars">
                
                <div class="section-header">
                    <div class="header-group">
                        <h3>Customer Messages</h3>
                    </div>
                     <div class="section-actions">
                        <span class="stat-badge new-messages">
                            <?= count($cars) ?> Total
                        </span>
                    </div>
                </div>
            

                <div class="cars-grid">
                    <?php foreach ($cars as $car): ?>
                    <div class="car-card">
                        <img src="../../assets/img/<?= htmlspecialchars($car['image']) ?>" alt="Car Image">
                        <div class="car-info">
                            <h4><?= htmlspecialchars($car['model']) ?></h4>
                            <p>Agency: <?= htmlspecialchars($car['agency_name']) ?></p>
                            <p>Price: $<?= htmlspecialchars($car['price_per_day']) ?>/day</p>
                            <p>Status: <?= htmlspecialchars($car['status']) ?></p>
                        </div>
                        <form action="/carRental/views/includes/delete.php" method="POST">
                            <input type="hidden" name="type" value="car">
                            <input type="hidden" name="id" value="<?= $car['id'] ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            
             <!-- Messages Section -->
            <section class="content-section" id="messages">
                <div class="section-header">
                    <div class="header-group">
                        <h3>Customer Messages</h3>
                    </div>
                    <div class="section-actions">
                        <span class="stat-badge new-messages">
                            <?= count($messages) ?> Total
                        </span>
                    </div>
                </div>

                <div class="messages-list">
                    <?php foreach ($messages as $message): ?>
                    <div class="message-card">
                        <div class="message-header">
                            <div class="sender-info">
                                <div class="email-badge">
                                    <div class="contact-details">
                                        <span class="detail-label">From:</span>
                                        <span class="message-email"><?= htmlspecialchars($message['email']) ?></span>
                                    </div>
                                </div>
                                <div class="message-timeline">
                                    <span class="timeline-badge">
                                        <?= date('M j, Y', strtotime($message['created_at'])) ?>
                                    </span>
                                    <span class="timeline-time">
                                        <?= date('H:i', strtotime($message['created_at'])) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="subject-wrapper">
                                <h4 class="message-subject">
                                    <span class="subject-label">Subject:</span>
                                    <?= htmlspecialchars($message['object']) ?>
                                </h4>
                            </div>
                        </div>
                    
                        <div class="message-content-card">
                            <div class="content-header">
                                <span class="content-label">Message Content:</span>
                            </div>
                            <div class="message-content scrollable-content">
                                <?= nl2br(htmlspecialchars($message['content'])) ?>
                            </div>
                        </div>
                    
                        <div class="message-actions">
                            <form action="delete.php" method="POST" class="delete-form">
                                <input type="hidden" name="type" value="message">
                                <input type="hidden" name="id" value="<?= $message['id'] ?>">
                                <button type="submit" class="btn-delete">
                                    <span class="delete-label">Archive Message</span>
                                    <div class="delete-hover-effect"></div>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($messages)): ?>
                        <div class="no-messages">
                            <div class="empty-state">
                                <h4>No Messages Yet</h4>
                                <p>Your inbox is currently empty. Check back later!</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>


        </main>
    </div>

    <script src="/carRental/controller/scripts/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

