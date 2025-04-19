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
                <li class="nav-item active" data-target="users"><a href="#">Users</a></li>
                <li class="nav-item" data-target="agencies"><a href="#">Agencies</a></li>
                <li class="nav-item" data-target="cars"><a href="#">Available Cars</a></li>
                <li class="nav-item" data-target="messages"><a href="#">Messages</a></li>
                <li class="nav-item"><a href="/carRental/views/auth/logout.php">Log Out</a></li>
            </ul>
        </nav>
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Users Section -->
            <section class="content-section active" id="users">
                <div class="section-header">
                    <h3>Manage Users (<?= count($clients) ?>)</h3>
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
                    <h3>Manage Agencies (<?= count($agencies) ?>)</h3>
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
                    <h3>Manage Cars (<?= count($cars) ?>)</h3>
                    <div class="input-group">
                        <div class="form-outline" data-mdb-input-init>
                            <input id="search-focus" type="search" id="form1" class="form-control" />
                            <label class="form-label" for="form1">Search</label>
                        </div>
                        <button type="button" class="btn btn-primary" data-mdb-ripple-init>
                            <i class="fas fa-search"></i>
                        </button>
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
            <i class="fas fa-envelope-open-text header-icon"></i>
            <h3>Customer Messages</h3>
        </div>
        <div class="section-actions">
            <div class="message-stats">
                <span class="stat-badge new-messages">
                    <i class="fas fa-star"></i>
                    <?= count($messages) ?> Total
                </span>
            </div>
        </div>
    </div>
    
    <div class="messages-list">
        <?php foreach ($messages as $message): ?>
        <div class="message-card <?= strtotime($message['created_at']) > time() - 86400 ? 'new-message' : '' ?>">
            <div class="message-header">
                <div class="sender-info">
                    <div class="email-badge">
                        <i class="fas fa-envelope"></i>
                        <div class="contact-details">
                            <span class="detail-label">From:</span>
                            <span class="message-email"><?= htmlspecialchars($message['email']) ?></span>
                        </div>
                    </div>
                    <div class="message-timeline">
                        <span class="timeline-badge">
                            <i class="fas fa-clock"></i>
                            <?= date('M j, Y', strtotime($message['created_at'])) ?>
                        </span>
                        <span class="timeline-time">
                            <?= date('H:i', strtotime($message['created_at'])) ?>
                        </span>
                    </div>
                </div>
                
                <div class="subject-wrapper">
                    <i class="fas fa-tag subject-icon"></i>
                    <h4 class="message-subject">
                        <span class="subject-label">Subject:</span>
                        <?= htmlspecialchars($message['object']) ?>
                    </h4>
                </div>
            </div>

            <div class="message-content-card">
                <div class="content-header">
                    <i class="fas fa-comment-dots content-icon"></i>
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
                        <i class="fas fa-trash-alt"></i>
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
                    <i class="fas fa-comments-slash"></i>
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
<script>
    
</script>
</body>
</html>

<style>
    /* Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Arial', sans-serif;
}

body {
    background-color: #f5f5f5;
}

/* Admin Container */
.admin-container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar Styles */
.admin-sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: white;
    padding: 20px;
    position: fixed;
    height: 100%;
}

.admin-title {
    color: #28a745;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #34495e;
}

.admin-nav {
    list-style: none;
}

.nav-item {
    margin: 10px 0;
    padding: 12px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.nav-item.active,
.nav-item:hover {
    background-color: #34495e;
}

.nav-item a {
    color: white;
    text-decoration: none;
    display: block;
}

/* Main Content Styles */
.admin-main {
    flex: 1;
    margin-left: 250px;
    padding: 30px;
}

/* Section Styles */
.content-section {
    display: none;
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.content-section.active {
    display: block;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

/* User/Car Cards */
.user-card,
.car-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    margin: 10px 0;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.user-actions,
.car-info {
    display: flex;
    gap: 10px;
}

/* Button Styles */
.btn-view {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-delete {
    background-color: #e53e3e;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-add {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
}

/* Cars Grid */
.cars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.car-card {
    flex-direction: column;
    align-items: flex-start;
}

.car-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 5px;
}

/* Add Agency Form */
.add-agency-form {
    display: none;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.add-agency-form input {
    display: block;
    width: 100%;
    margin: 10px 0;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-cancel {
    background-color: #6c757d;
}

.btn-save {
    background-color: #28a745;
}



/* Add these new styles */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #34495e;
    --success-color: #28a745;
    --danger-color: #e53e3e;
    --hover-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.user-card:hover,
.car-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: var(--hover-transition);
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.confirm-dialog {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

@media (max-width: 768px) {
    .admin-container {
        flex-direction: column;
    }
    
    .admin-sidebar {
        width: 100%;
        position: relative;
        height: auto;
    }
    
    .admin-main {
        margin-left: 0;
        padding: 1rem;
    }
    
    .user-card {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .user-actions {
        width: 100%;
        justify-content: flex-end;
        margin-top: 1rem;
    }
}

/* Add to existing button styles */
.btn-view:hover,
.btn-add:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.btn-delete:hover {
    background-color: #c53030;
}

/* Add image fallback styling */
.car-card img {
    background: #f0f0f0;
    position: relative;
}

.car-card img::after {
    content: "Image not available";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #666;
    font-size: 0.9rem;
}


/* Premium Styling */
.content-section {
    background: #f8fafc;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.03), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
}

.section-header {
    padding: 1.5rem 2rem;
    background: #ffffff;
    border-radius: 16px 16px 0 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
}

.header-group {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon {
    font-size: 1.75rem;
    color: #3b82f6;
}

.message-stats {
    display: flex;
    gap: 1rem;
}

.stat-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
}

.new-messages {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}



.message-card {
    background: white;
    border-radius: 12px;
    border: 1px solid #f1f5f9;
    margin: 1.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.message-card.new-message {
    border-left: 4px solid #3b82f6;
    background: #f8fafc;
}

.sender-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.email-badge {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.email-badge i {
    font-size: 1.25rem;
    color: #64748b;
}

.contact-details {
    display: flex;
    flex-direction: column;
}

.detail-label {
    font-size: 0.75rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.message-timeline {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.timeline-badge {
    background: #f1f5f9;
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.85rem;
}

.subject-wrapper {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: #f8fafc;
}

.subject-icon {
    color: #94a3b8;
    font-size: 1.1rem;
}

.subject-label {
    color: #64748b;
    font-size: 0.9rem;
    margin-right: 0.5rem;
}

.message-content-card {
    padding: 2rem;
    background: white;
}

.content-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.content-icon {
    color: #3b82f6;
    font-size: 1.5rem;
}

.content-label {
    color: #1e293b;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.scrollable-content {
    max-height: 200px;
    overflow-y: auto;
    padding-right: 1rem;
    line-height: 1.7;
    color: #475569;
}

.btn-delete {
    background: #fff1f2;
    color: #dc2626;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    border: 1px solid #ffe4e6;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.btn-delete:hover {
    background: #ffe4e6;
    transform: translateY(-1px);
}

.delete-hover-effect {
    position: absolute;
    background: rgba(220, 38, 38, 0.1);
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.btn-delete:hover .delete-hover-effect {
    opacity: 1;
}

.empty-state {
    text-align: center;
    padding: 4rem;
    background: white;
    border-radius: 16px;
    margin: 2rem;
}

.empty-state i {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 1.5rem;
}
</style>