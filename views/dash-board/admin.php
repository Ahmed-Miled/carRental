<?php
// Strict error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1); // Turn off in production, log errors instead

session_start(); // Start the session

/*
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page if not authenticated
    header("Location: /carRental/views/auth/authentification.php?role=admin"); // Adjust login path if needed
    exit();
}
*/
// (Remove the comments above once your login system is in place and sets the session variable)


// Include necessary configuration and functions
require_once __DIR__ . '/../../config/paths.php'; // Defines ROOT_DIR
require_once ROOT_DIR . '/config/database.php'; // Establishes $pdo connection
require_once ROOT_DIR . '/controller/process_messages.php'; // Function to get messages

// --- Variable Initialization ---
$clients = [];
$agencies = [];
$cars = [];
$messages = [];
$activeBookings = 0;
$availableCarsCount = 0;
$dbError = null; // To store potential database error messages

// --- Fetch Data from Database ---
try {
    // Get all clients
    $clientStmt = $pdo->prepare("SELECT id, name, email, phoneNumber FROM users WHERE type = 'client' ORDER BY created_at DESC");
    $clientStmt->execute();
    $clients = $clientStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all agencies
    $agencyStmt = $pdo->prepare("SELECT id, name, email, phoneNumber FROM users WHERE type = 'agency' ORDER BY created_at DESC");
    $agencyStmt->execute();
    $agencies = $agencyStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all cars with agency info
    $carStmt = $pdo->prepare("
        SELECT c.id, c.model, c.image, c.price_per_day, c.status, c.agency_id, u.name AS agency_name
        FROM cars c
        JOIN users u ON c.agency_id = u.id
        ORDER BY c.created_at DESC
    ");
    $carStmt->execute();
    $cars = $carStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get messages using the function from process_messages.php
    // Assuming getMessages returns an array and might look like:
    // [ ['id'=>1, 'name'=>'John', 'email'=>'j@d.com', 'object'=>'Help', 'content'=>'...', 'created_at'=>'...', 'is_read'=>0], ... ]
    $messages = getMessages($pdo); // Adapt if getMessages returns different structure or handles errors differently

    // Get active bookings count
    $activeBookingsStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM reservations
        WHERE status IN ('active', 'confirmed') 
          AND end_date >= CURDATE() -- Check if booking is ongoing or future
    ");
    $activeBookingsStmt->execute();
    $activeBookings = $activeBookingsStmt->fetchColumn() ?: 0; // Use null coalescing operator for safety

    // Get truly available cars count (not booked currently)
    $availableCarsStmt = $pdo->prepare("
        SELECT COUNT(DISTINCT c.id)
        FROM cars c
        WHERE c.status = 'available'
        AND NOT EXISTS (
            SELECT 1
            FROM reservations r
            WHERE r.car_id = c.id
              AND r.status IN ('active', 'confirmed') -- Match active statuses
              AND NOW() BETWEEN r.start_date AND r.end_date
        )
    ");
    $availableCarsStmt->execute();
    $availableCarsCount = $availableCarsStmt->fetchColumn() ?: 0; // Use null coalescing operator for safety

} catch (PDOException $e) {
    error_log("Database error in admin.php: " . $e->getMessage());
    $dbError = "An error occurred while fetching data. Please check the logs."; // User-friendly message
} catch (Exception $e) {
    error_log("General error in admin.php: " . $e->getMessage());
    $dbError = "An unexpected error occurred. Please check the logs.";
}

// --- Helper Function for Status Badges ---
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'available': return 'status-badge-success'; // Defined in admin.css
        case 'rented':
        case 'booked': return 'status-badge-warning'; // Defined in admin.css
        case 'maintenance':
        case 'unavailable': return 'status-badge-danger'; // Defined in admin.css
        default: return 'status-badge-secondary'; // Defined in admin.css
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CarRent</title>

    <link rel="icon" href="/carRental/assets/favicon.ico" type="image/x-icon"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/carRental/assets/css/admin.css"> 
</head>

<body>
    <!-- Main Admin Container -->
    <div class="admin-container">

        <!-- Left Navigation Sidebar (Matches CSS Structure) -->
        <nav class="admin-sidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <a href="/carRental/views/admin/admin.php" class="admin-logo-link">
                    <i class="fas fa-car-side logo-icon"></i> <!-- Logo Icon -->
                    <span class="admin-title">CarRent Admin</span> <!-- Title -->
                </a>
            </div>
            <!-- Navigation Links -->
            <ul class="admin-nav">
                <!-- Each li is a nav-item, active class added by JS -->
                <li class="nav-item active" data-target="status">
                    <a href="#status">
                        <i class="nav-icon fas fa-tachometer-alt"></i> Status <!-- Icon + Text -->
                    </a>
                </li>
                <li class="nav-item" data-target="users">
                    <a href="#users">
                        <i class="nav-icon fas fa-users"></i> Clients
                    </a>
                </li>
                <li class="nav-item" data-target="agencies">
                    <a href="#agencies">
                        <i class="nav-icon fas fa-building"></i> Agencies
                    </a>
                </li>
                <li class="nav-item" data-target="cars">
                    <a href="#cars">
                        <i class="nav-icon fas fa-car"></i> Cars
                    </a>
                </li>
                <li class="nav-item" data-target="messages">
                    <a href="#messages">
                        <i class="nav-icon fas fa-envelope"></i> Messages
                        <!-- Badge for unread messages count -->
                        <?php
                        // Assuming $messages is an array and each message has an 'is_read' key (0 for unread)
                        $unreadCount = 0;
                        if (is_array($messages)) {
                             $unreadCount = count(array_filter($messages, fn($msg) => isset($msg['is_read']) && !$msg['is_read']));
                        }
                        if ($unreadCount > 0):
                        ?>
                            <span class="nav-badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <!-- Logout Link -->
                <li class="nav-item logout-item">
                    <a href="/carRental/views/auth/logout.php"> <!-- Ensure logout path is correct -->
                        <i class="nav-icon fas fa-sign-out-alt"></i> Log Out
                    </a>
                </li>
            </ul>
            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <p>© <?= date('Y') ?> CarRent. All rights reserved.</p>
            </div>
        </nav>

        <!-- Main Content Area (Matches CSS Structure) -->
        <main class="admin-main">

            <!-- Display Database or General Errors -->
            <?php if ($dbError): ?>
                <div class="alert alert-danger"> <!-- Alert styling from admin.css -->
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($dbError) ?>
                </div>
            <?php endif; ?>

            <!-- Status Section (Matches CSS Structure) -->
            <section class="content-section active" id="status">
                <!-- Section Header -->
                <div class="section-header">
                    <h3><i class="fas fa-chart-line"></i> Quick Status Overview</h3>
                </div>
                <!-- Section Content -->
                <div class="section-content">
                    <!-- Status Cards Grid -->
                    <div class="status-cards">
                        <!-- Card for Total Users -->
                        <div class="status-card card-users"> <!-- Specific class for potential styling -->
                            <div class="card-icon"><i class="fas fa-users"></i></div>
                            <div class="card-content">
                                <h4>Total Users</h4>
                                <p class="card-value"><?= count($clients) + count($agencies) ?></p>
                                <span class="card-subtext">(<?= count($clients) ?> Clients, <?= count($agencies) ?> Agencies)</span>
                            </div>
                        </div>
                        <!-- Card for Active Bookings -->
                        <div class="status-card card-bookings">
                            <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="card-content">
                                <h4>Active Bookings</h4>
                                <p class="card-value"><?= $activeBookings ?></p>
                                <span class="card-subtext">Ongoing or Upcoming</span>
                            </div>
                        </div>
                        <!-- Card for Available Cars -->
                        <div class="status-card card-cars">
                            <div class="card-icon"><i class="fas fa-car-side"></i></div>
                            <div class="card-content">
                                <h4>Available Cars Now</h4>
                                <p class="card-value"><?= $availableCarsCount ?></p>
                                <span class="card-subtext">Ready for Rent</span>
                            </div>
                        </div>
                         <!-- Card for Unread Messages -->
                        <div class="status-card card-messages">
                            <div class="card-icon"><i class="fas fa-envelope-open-text"></i></div>
                            <div class="card-content">
                                <h4>Unread Messages</h4>
                                <p class="card-value"><?= $unreadCount ?></p> <!-- Use calculated unread count -->
                            </div>
                        </div>
                    </div> 

                    <!-- Quick Stats / Recent Activity -->
                    <div class="quick-stats"> <!-- Styling from admin.css -->
                        <h4>Recent Activity Summary</h4>
                        <ul class="activity-list"> <!-- Styling from admin.css -->
                            <li><i class="fas fa-user-plus"></i> <span>2</span> New Signups (Today) <span class="text-muted">(Example - Needs Real Data)</span></li>
                            <li><i class="fas fa-car"></i> <span><?= count($cars) ?></span> Total Cars Listed</li>
                            <li><i class="fas fa-check-circle text-success"></i> System Status: Operational</li>
                        </ul>
                    </div>
                </div> 
            </section>

            <!-- Users (Clients) Section (Matches CSS Structure) -->
            <section class="content-section" id="users">
                <div class="section-header">
                    <h3><i class="fas fa-users"></i> Manage Clients</h3>
                     <div class="section-actions">
                        <!-- Stat Badge for total count -->
                        <span class="stat-badge info-badge"> 
                            <i class="fas fa-user-check"></i> <?= count($clients) ?> Total Clients
                        </span>
                    </div>
                </div>
                 <div class="section-content">
                    <?php if (empty($clients) && !$dbError): ?> <!-- Show only if no DB error AND no clients -->
                        <div class="alert alert-info">No client accounts found.</div> <!-- Alert styling -->
                    <?php elseif (!empty($clients)): ?>
                        <!-- Responsive Table Wrapper -->
                        <div class="table-responsive">
                            <table class="data-table"> <!-- Table styling from admin.css -->
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clients as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['name']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['phoneNumber']) ?></td>
                                        <!-- Action Cell for buttons -->
                                        <td class="action-cell">
                                            <!-- Delete Form -->
                                            <form action="/carRental/controller/delete_handler.php" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this client? This action cannot be undone.');">
                                                <input type="hidden" name="type" value="user">
                                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                <!-- Button with specific classes from admin.css -->
                                                <button type="submit" class="btn btn-delete">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div> <!-- End table-responsive -->
                    <?php endif; ?>
                 </div> <!-- End section-content -->
            </section> <!-- End users section -->

            <!-- Agencies Section (Matches CSS Structure) -->
            <section class="content-section" id="agencies">
                <div class="section-header">
                    <h3><i class="fas fa-building"></i> Manage Agencies</h3>
                    <div class="section-actions">
                        <span class="stat-badge info-badge">
                           <i class="fas fa-building-user"></i> <?= count($agencies) ?> Total Agencies
                        </span>
                        <!-- Add Agency Button -->
                        <button class="btn btn-add" id="add-agency-btn"><i class="fas fa-plus"></i> Add Agency</button>
                    </div>
                </div>
                 <div class="section-content">
                    <!-- Add Agency Form (Initially Hidden, controlled by admin.js) -->
                    <form class="add-agency-form" id="add-agency-form" action="/carRental/controller/add_agency_handler.php" method="POST" style="display: none;"> <!-- Ensure action path is correct -->
                        <h4>New Agency Details</h4>
                        <!-- Form Grid for layout -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="agency-name">Agency Name</label>
                                <input type="text" id="agency-name" name="name" placeholder="Enter Agency Name" required>
                            </div>
                            <div class="form-group">
                                <label for="agency-email">Email</label>
                                <input type="email" id="agency-email" name="email" placeholder="Enter Email Address" required>
                            </div>
                            <div class="form-group">
                                <label for="agency-phone">Phone Number</label>
                                <input type="tel" id="agency-phone" name="phone" placeholder="Enter Phone Number" required>
                            </div>
                             <div class="form-group">
                                <label for="agency-password">Password</label>
                                <input type="password" id="agency-password" name="password" placeholder="Create a Password" required>
                            </div>
                        </div>
                        <!-- Form Actions (Cancel/Save) -->
                        <div class="form-actions">
                            <button type="button" class="btn btn-cancel" id="cancel-add-agency-btn">Cancel</button>
                            <button type="submit" class="btn btn-save">Save Agency</button>
                        </div>
                    </form> <!-- End add-agency-form -->

                    <!-- Agencies List Table -->
                     <?php if (empty($agencies) && !$dbError): ?>
                        <div class="alert alert-info">No agency accounts found.</div>
                    <?php elseif (!empty($agencies)): ?>
                        <div class="table-responsive">
                             <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($agencies as $agency): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($agency['name']) ?></td>
                                        <td><?= htmlspecialchars($agency['email']) ?></td>
                                        <td><?= htmlspecialchars($agency['phoneNumber']) ?></td>
                                         <td class="action-cell">
                                             <!-- Delete Form -->
                                            <form action="/carRental/controller/delete_handler.php" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this agency? This may also affect associated cars and bookings.');">
                                                <input type="hidden" name="type" value="agency">
                                                <input type="hidden" name="id" value="<?= $agency['id'] ?>">
                                                <button type="submit" class="btn btn-delete">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div> <!-- End table-responsive -->
                    <?php endif; ?>
                 </div> <!-- End section-content -->
            </section> <!-- End agencies section -->

            <!-- Cars Section (Matches CSS Structure) -->
            <section class="content-section" id="cars">
                <div class="section-header">
                    <h3><i class="fas fa-car"></i> Manage Cars</h3>
                     <div class="section-actions">
                        <span class="stat-badge info-badge">
                            <i class="fas fa-car-side"></i> <?= count($cars) ?> Total Cars
                        </span>
                         <!-- Link to a dedicated "Add Car" page/form -->
                         <a href="/carRental/views/admin/add_car.php" class="btn btn-add"><i class="fas fa-plus"></i> Add Car</a> <!-- Ensure path is correct -->
                    </div>
                </div>
                 <div class="section-content">
                    <?php if (empty($cars) && !$dbError): ?>
                        <div class="alert alert-info">No cars found in the system.</div>
                    <?php elseif (!empty($cars)): ?>
                        <!-- Cars Grid Layout -->
                        <div class="cars-grid"> <!-- Grid styling from admin.css -->
                            <?php foreach ($cars as $car): ?>
                            <!-- Individual Car Card -->
                            <div class="car-card"> <!-- Card styling from admin.css -->
                                <!-- Car Image Container -->
                                <div class="car-image-container">
                                    <!-- Image with fallback -->
                                    <img src="/carRental/assets/img/<?= htmlspecialchars($car['image'] ?: 'default_car.png') ?>" alt="<?= htmlspecialchars($car['model']) ?>" loading="lazy" onerror="this.onerror=null; this.src='/carRental/assets/img/default_car.png';"> <!-- Ensure paths are correct -->
                                    <!-- Status Badge positioned absolutely -->
                                    <span class="status-badge <?= getStatusBadgeClass($car['status']) ?>"> <!-- Dynamic class from helper function -->
                                        <?= htmlspecialchars(ucfirst($car['status'])) ?>
                                    </span>
                                </div>
                                <!-- Car Information -->
                                <div class="car-info">
                                    <h4><?= htmlspecialchars($car['model']) ?></h4>
                                    <p class="car-agency"><i class="fas fa-building"></i> Agency: <?= htmlspecialchars($car['agency_name']) ?></p>
                                    <p class="car-price"><i class="fas fa-dollar-sign"></i> <?= htmlspecialchars(number_format((float)$car['price_per_day'], 2)) ?> / day</p> <!-- Format price -->
                                </div>
                                <!-- Car Actions -->
                                <div class="car-actions">
                                    <!-- Delete Form -->
                                    <form action="/carRental/controller/delete_handler.php" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this car?');">
                                        <input type="hidden" name="type" value="car">
                                        <input type="hidden" name="id" value="<?= $car['id'] ?>">
                                        <button type="submit" class="btn btn-delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div> <!-- End car-card -->
                            <?php endforeach; ?>
                        </div> <!-- End cars-grid -->
                    <?php endif; ?>
                 </div> <!-- End section-content -->
            </section> <!-- End cars section -->

             <!-- Messages Section (Matches CSS Structure) -->
            <section class="content-section" id="messages">
                <div class="section-header">
                    <h3><i class="fas fa-envelope"></i> Customer Messages</h3>
                    <div class="section-actions">
                        <span class="stat-badge info-badge">
                             <i class="fas fa-inbox"></i> <?= count($messages) ?> Messages Found
                        </span>
                        <!-- Optional: Add Mark All as Read button here -->
                    </div>
                </div>
                 <div class="section-content">
                     <!-- Messages List Container -->
                     <div class="messages-list"> <!-- List styling from admin.css -->
                        <?php if (empty($messages) && !$dbError): ?>
                            <!-- Empty State Message -->
                            <div class="empty-state"> <!-- Styling from admin.css -->
                                <i class="fas fa-folder-open"></i>
                                <h4>No Messages Yet</h4>
                                <p>Your inbox is currently empty. New messages will appear here.</p>
                            </div>
                        <?php elseif (!empty($messages)): ?>
                            <?php foreach ($messages as $message): ?>
                            <!-- Individual Message Card -->
                            <div class="message-card <?= (isset($message['is_read']) && !$message['is_read']) ? 'new-message' : '' ?>"> <!-- Add 'new-message' class if unread -->
                                <!-- Message Header -->
                                <div class="message-header">
                                    <div class="sender-info"> <!-- Flex container for sender/time -->
                                        <!-- Contact Details -->
                                        <div class="contact-details">
                                            <span class="detail-label">From:</span>
                                            <span class="message-email">
                                                <i class="fas fa-at"></i> <?= htmlspecialchars($message['email'] ?? 'N/A') ?>
                                            </span>
                                            <?php if (!empty($message['name'])): ?>
                                            <span class="message-sender-name">
                                                 <i class="fas fa-user"></i> <?= htmlspecialchars($message['name']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Message Timeline -->
                                         <div class="message-timeline">
                                            <span class="timeline-badge">
                                                <i class="fas fa-calendar-alt"></i> <?= isset($message['created_at']) ? date('M j, Y', strtotime($message['created_at'])) : 'N/A' ?>
                                            </span>
                                            <span class="timeline-time">
                                                <i class="fas fa-clock"></i> <?= isset($message['created_at']) ? date('H:i', strtotime($message['created_at'])) : '' ?>
                                            </span>
                                        </div>
                                    </div> <!-- End sender-info -->
                                </div> <!-- End message-header -->
                                <!-- Message Subject -->
                                <div class="message-subject-line">
                                     <span class="subject-label">Subject:</span>
                                     <span class="message-subject"><?= htmlspecialchars($message['object'] ?? 'No Subject') ?></span>
                                </div>
                                <!-- Message Content -->
                                <div class="message-content-card">
                                    <div class="message-content scrollable-content"> <!-- Scrollable content -->
                                        <?= nl2br(htmlspecialchars($message['content'] ?? 'No Content')) ?>
                                    </div>
                                </div>
                                <!-- Message Actions -->
                                <div class="message-actions">
                                    <!-- Delete Form -->
                                    <form action="/carRental/controller/delete.php" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                        <input type="hidden" name="type" value="message">
                                        <input type="hidden" name="id" value="<?= $message['id'] ?>">
                                        <button type="submit" class="btn btn-delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                    <!-- Optional: Add Reply/Mark as Read Buttons here -->
                                </div>
                            </div> <!-- End message-card -->
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div> <!-- End messages-list -->
                 </div> <!-- End section-content -->
            </section> <!-- End messages section -->

        </main> <!-- End admin-main -->
    </div> <!-- End admin-container -->

    <!-- Essential JavaScript for Interactivity -->
    <script src="/carRental/controller/scripts/admin.js"></script> <!-- Ensure path is correct -->

</body>
</html>