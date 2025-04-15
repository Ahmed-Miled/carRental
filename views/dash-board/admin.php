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

//session_start();
require __DIR__ . '/../../config/database.php';
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
/*
    // Get all cars with agency info
    $carStmt = $pdo->prepare("
        SELECT cars.*, users.name AS agency_name 
        FROM cars 
        JOIN users ON cars.agency_id = users.id
    ");
    $carStmt->execute();
    $cars = $carStmt->fetchAll(PDO::FETCH_ASSOC);
*/
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
                <li class="nav-item"><a href="logout.php">Log Out</a></li>
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
                <!-- Agencies List -->
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
                </div>
                <div class="cars-grid">
                    <?php foreach ($cars as $car): ?>
                    <div class="car-card">
                        <img src="../uploads/<?= htmlspecialchars($car['image']) ?>" alt="Car Image">
                        <div class="car-info">
                            <h4><?= htmlspecialchars($car['model']) ?></h4>
                            <p>Agency: <?= htmlspecialchars($car['agency_name']) ?></p>
                            <p>Price: $<?= htmlspecialchars($car['price_per_day']) ?>/day</p>
                            <p>Status: <?= htmlspecialchars($car['status']) ?></p>
                        </div>
                        <form action="delete.php" method="POST">
                            <input type="hidden" name="type" value="car">
                            <input type="hidden" name="id" value="<?= $car['id'] ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('heelo world');
            // Navigation Logic
            document.querySelectorAll('.nav-item').forEach(item => {
                console.log('item clicked');
                item.addEventListener('click', function() {
                    // Remove active class from all items
                    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
                    console.log('ezra');
                    // Add active class to clicked item
                    this.classList.add('active');
                    const target = this.dataset.target;
                    if(target) document.getElementById(target).classList.add('active');
                });
            });

            // Show/Hide Add Agency Form
            const addBtn = document.querySelector('.btn-add');
            const agencyForm = document.querySelector('.add-agency-form');
            if(addBtn && agencyForm) {
                addBtn.addEventListener('click', () => agencyForm.style.display = 'block');
                document.querySelector('.btn-cancel').addEventListener('click', () => agencyForm.style.display = 'none');
            }
        }) 
    </script>
</body>
</html>


