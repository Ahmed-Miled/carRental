<?php
// Strict error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1); // Turn off in production, log errors instead


/*
if (!isset($_SESSION['admin_logged_in'])) {

    header('Location: /carRental/views/auth/authentification.php');
    exit();
}

*/


// Include necessary configuration and functions
require_once __DIR__ . '/../../config/paths.php'; // Defines ROOT_DIR
require_once ROOT_DIR . '/config/database.php'; // Establishes $pdo connection
require_once ROOT_DIR . '/controller/process_messages.php'; // Function to get messages
require_once __DIR__ . '/../../models/admin.php';

// --- Variable Initialization ---
$clients = getClients($pdo);
$agencies = getAgencys($pdo);
$cars = [];
$messages = [];
$activeBookings = 0;
$availableCarsCount = 0;
$dbError = null; // To store potential database error messages

// --- Fetch Data from Database ---
try {

    // Get all cars with agency info
    $carStmt = $pdo->prepare("
        SELECT * 
        FROM cars c, agency a WHERE c.agency_id = a.id
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

echo "<script>console.log(" . json_encode($cars) . ");</script>";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord d'administration - CarRent</title>

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
                    <i class="fas fa-car-side logo-icon"></i> 
                    <span class="admin-title">CarRent Administrateur</span>
                </a>
            </div>
            <!-- Navigation Links -->
            <ul class="admin-nav">
                
                <li class="nav-item active" data-target="status">
                    <a href="#status">
                        <i class="nav-icon fas fa-tachometer-alt"></i> Statut 
                    </a>
                </li>
                <li class="nav-item" data-target="users">
                    <a href="#users">
                        <i class="nav-icon fas fa-users"></i> Clients
                    </a>
                </li>
                <li class="nav-item" data-target="agencies">
                    <a href="#agencies">
                        <i class="nav-icon fas fa-building"></i> Agences
                    </a>
                </li>
                <li class="nav-item" data-target="cars">
                    <a href="#cars">
                        <i class="nav-icon fas fa-car"></i> Voitures
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
                    
                    <a href="/carRental/views/auth/logout.php"> 
                        <i class="nav-icon fas fa-sign-out-alt"></i> Se déconnecter
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <p>© <?= date('Y') ?> CarRent. Tous droits réservés.</p>
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
                    <h3><i class="fas fa-chart-line"></i> Aperçu rapide de l'état</h3>
                </div>
                
                <div class="section-content">
                    
                    <div class="status-cards">
                        
                        <div class="status-card card-users"> <!-- Specific class for potential styling -->
                            <div class="card-icon"><i class="fas fa-users"></i></div>
                            <div class="card-content">
                                <h4>Nombre total d'utilisateurs</h4>
                                <p class="card-value"><?= getUsersCount($pdo, 'all') ?></p>
                                <span class="card-subtext">(<?= getUsersCount($pdo, 'client') ?> Clients, <?= getUsersCount($pdo, 'agency') ?> Agences)</span>
                            </div>
                        </div>
                        <!-- Card for Active Bookings -->
                        <div class="status-card card-bookings">
                            <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="card-content">
                                <h4>Réservations actives</h4>
                                <p class="card-value"><?= $activeBookings ?></p>
                                <span class="card-subtext">En cours ou à venir</span>
                            </div>
                        </div>
                        <!-- Card for Available Cars -->
                        <div class="status-card card-cars">
                            <div class="card-icon"><i class="fas fa-car-side"></i></div>
                            <div class="card-content">
                                <h4>Voitures disponibles maintenant</h4>
                                <p class="card-value"><?= $availableCarsCount ?></p>
                                <span class="card-subtext">Prêt à louer</span>
                            </div>
                        </div>
                         <!-- Card for Unread Messages -->
                        <div class="status-card card-messages">
                            <div class="card-icon"><i class="fas fa-envelope-open-text"></i></div>
                            <div class="card-content">
                                <h4>Messages non lus</h4>
                                <p class="card-value"><?= $unreadCount ?></p> <!-- Use calculated unread count -->
                            </div>
                        </div>
                    </div> 

                    <!-- Quick Stats / Recent Activity -->
                    <div class="quick-stats"> <!-- Styling from admin.css -->
                        <h4>Résumé des activités récentes</h4>
                        <ul class="activity-list"> <!-- Styling from admin.css -->
                            <li><i class="fas fa-user-plus"></i> <span>2</span> Nouvelles inscriptions (aujourd'hui) <span class="text-muted">(Exemple - Nécessite des données réelles)</span></li>
                            <li><i class="fas fa-car"></i> <span><?= count($cars) ?></span> Nombre total de voitures répertoriées</li>
                            <li><i class="fas fa-check-circle text-success"></i> État du système : opérationnel</li>
                        </ul>
                    </div>
                </div> 
            </section>

            
            <section class="content-section" id="users">
                <div class="section-header">
                    <h3><i class="fas fa-users"></i> Gérer les clients</h3>
                     <div class="section-actions">
                        
                        <span class="stat-badge info-badge"> 
                            <i class="fas fa-user-check"></i> <?= getUsersCount($pdo, 'client') ?> Total Clients
                        </span>
                    </div>
                </div>
                 <div class="section-content">
                    <?php if (empty($clients) && !$dbError): ?> <!-- Show only if no DB error AND no clients -->
                        <div class="alert alert-info">Aucun compte client trouvé.</div> <!-- Alert styling -->
                    <?php elseif (!empty($clients)): ?>
                        
                        <div class="table-responsive">
                            <table class="data-table"> 
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Actes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clients as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['fullName']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['phoneNumber']) ?></td>
                                        
                                        <td class="action-cell">
                                            
                                            <form action="/carRental/controller/process_messages.php" method="POST" class="delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ? Cette action est irréversible.');">
                                                <input type="hidden" name="type" value="user">
                                                <input type="hidden" name="action" value="deletClient">
                                                <input type="hidden" name="client_id" value="<?= $user['id'] ?>">
                                                
                                                <button type="submit" class="btn btn-delete">
                                                    <i class="fas fa-trash-alt"></i> Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div> 
                    <?php endif; ?>
                 </div> 
            </section> 

            
            <section class="content-section" id="agencies">
                <div class="section-header">
                    <h3><i class="fas fa-building"></i> Gérer les agences</h3>
                    <div class="section-actions">
                        <span class="stat-badge info-badge">
                           <i class="fas fa-building-user"></i> <?= getUsersCount($pdo, 'agency') ?> Total des agences
                        </span>
                        
                        <button class="btn btn-add" id="add-agency-btn"><i class="fas fa-plus"></i> Ajouter une agence</button>
                    </div>
                </div>
                 <div class="section-content">
                    <!-- Add Agency Form (Initially Hidden, controlled by admin.js) -->
                    <form class="add-agency-form" id="add-agency-form" action="/carRental/controller/traitemant.php" method="POST" style="display: none;"> <!-- Ensure action path is correct -->
                    <input type="hidden" name="action" value="signup">
                    <input type="hidden" name="role" value="admin">    
                    <h4>Détails de la nouvelle agence</h4>
                        <!-- Form Grid for layout -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="user_name">Nom de l'agence</label>
                                <input type="text" id="user_name" name="user_name" placeholder="Enter Agency Name" required>
                            </div>
                            <div class="form-group">
                                <label for="phoneNumber">Numéro de téléphone</label>
                                <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="Enter Phone Number" required>
                            </div>
                            <div class="form-group">
                                <label for="user_address">Adresse</label>
                                <input type="text" id="user_address" name="user_address" placeholder="Enter agency Address" required>
                            </div>
                            <div class="form-group">
                                <label for="user_email">Email</label>
                                <input type="email" id="user_email" name="user_email" placeholder="Enter Email Address" required>
                            </div>
                             <div class="form-group">
                                <label for="user_password">Mot de passe</label>
                                <input type="password" id="user_password" name="user_password" placeholder="Create a Password" required>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-cancel" id="cancel-add-agency-btn">Annuler</button>
                            <button type="submit" class="btn btn-save">Enregistrer l'agence</button>
                        </div>
                    </form>
                    
                    <!-- Agencies List Table -->
                     <?php if (empty($agencies) && !$dbError): ?>
                        <div class="alert alert-info">Aucun compte d'agence trouvé.</div>
                    <?php elseif (!empty($agencies)): ?>
                        <div class="table-responsive">
                             <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Numéro de téléphone</th>
                                        <th>Adresse</th>
                                        <th>Actes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($agencies as $agency): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($agency['fullName']) ?></td>
                                        <td><?= htmlspecialchars($agency['email']) ?></td>
                                        <td><?= htmlspecialchars($agency['phoneNumber']) ?></td>
                                        <td><?= htmlspecialchars($agency['address']) ?></td>
                                         <td class="action-cell">
                                             <!-- Delete Form -->
                                            <form action="/carRental/controller/process_messages.php" method="POST" class="delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette agence ? Cela pourrait également affecter les voitures et réservations associées.');">
                                                <input type="hidden" name="type" value="agency">
                                                <input type="hidden" name="action" value="deletAgency">
                                                <input type="hidden" name="agency_id" value="<?= $agency['id'] ?>">
                                                <button type="submit" class="btn btn-delete">
                                                    <i class="fas fa-trash-alt"></i> Supprimer
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
            </section>

            <!-- Cars Section (Matches CSS Structure) -->
            <section class="content-section" id="cars">
                <div class="section-header">
                    <h3><i class="fas fa-car"></i> Gérer les voitures</h3>
                     <div class="section-actions">
                        <span class="stat-badge info-badge">
                            <i class="fas fa-car-side"></i> <?= count($cars) ?>Total des voitures
                        </span>
                    </div>
                </div>
                 <div class="section-content">
                    <?php if (empty($cars) && !$dbError): ?>
                        <div class="alert alert-info">Aucune voiture trouvée dans le système.</div>
                    <?php elseif (!empty($cars)): ?>
                        
                        <div class="cars-grid">
                            <?php foreach ($cars as $car): ?>
                                
                            <div class="car-card">
                                
                                <div class="car-image-container">
                                    
                                    <img src="/carRental/assets/img/<?= htmlspecialchars($car['image'] ?: 'default_car.png') ?>" alt="<?= htmlspecialchars($car['model']) ?>" loading="lazy" onerror="this.onerror=null; this.src='/carRental/assets/img/default_car.png';">
                                    
                                    <span class="status-badge <?= getStatusBadgeClass($car['status']) ?>"> 
                                        <?= htmlspecialchars(ucfirst($car['status'])) ?>
                                    </span>
                                </div>
                                <!-- Car Information -->
                                <div class="car-info">
                                    <h4><?= htmlspecialchars($car['model']) ?></h4>
                                    <p class="car-agency"><i class="fas fa-building"></i> Agence: <?= htmlspecialchars($car['fullName']) ?></p>
                                    <p class="car-price"><i class="fas fa-dollar-sign"></i> <?= htmlspecialchars(number_format((float)$car['price_per_day'], 2)) ?> / Jour</p> 
                                </div>
                                
                                <div class="car-actions">
                                    
                                    <form action="/carRental/controller/delete_handler.php" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this car?');">
                                        <input type="hidden" name="type" value="car">
                                        <input type="hidden" name="id" value="<?= $car['id'] ?>">
                                        <button type="submit" class="btn btn-delete">
                                            <i class="fas fa-trash-alt"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div> 
                            <?php endforeach; ?>
                        </div> 
                    <?php endif; ?>
                 </div> <!-- End section-content -->
            </section> <!-- End cars section -->

             <!-- Messages Section (Matches CSS Structure) -->
            <section class="content-section" id="messages">
                <div class="section-header">
                    <h3><i class="fas fa-envelope"></i> Messages clients</h3>
                    <div class="section-actions">
                        <span class="stat-badge info-badge">
                             <i class="fas fa-inbox"></i> <?= count($messages) ?> Messages trouvés
                        </span>
                        
                    </div>
                </div>
                 <div class="section-content">
                     <!-- Messages List Container -->
                     <div class="messages-list"> 
                        <?php if (empty($messages) && !$dbError): ?>
                            <!-- Empty State Message -->
                            <div class="empty-state"> 
                                <i class="fas fa-folder-open"></i>
                                <h4>Aucun message pour le moment</h4>
                                <p>Votre boîte de réception est actuellement vide. Les nouveaux messages apparaîtront ici.</p>
                            </div>
                        <?php elseif (!empty($messages)): ?>
                            <?php foreach ($messages as $message): ?>
                            <!-- Individual Message Card -->
                            <div class="message-card <?= (isset($message['is_read']) && !$message['is_read']) ? 'new-message' : '' ?>"> 
                                
                                <div class="message-header">
                                    <div class="sender-info"> 
                                        
                                        <div class="contact-details">
                                            <span class="detail-label">De:</span>
                                            <span class="message-email">
                                                <?= htmlspecialchars($message['email'] ?? 'N/A') ?>
                                            </span>
                                            <?php if (!empty($message['name'])): ?>
                                            <span class="message-sender-name">
                                                 <i class="fas fa-user"></i> <?= htmlspecialchars($message['name']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                         <div class="message-timeline">
                                            <span class="timeline-badge">
                                                <i class="fas fa-calendar-alt"></i> <?= isset($message['created_at']) ? date('M j, Y', strtotime($message['created_at'])) : 'N/A' ?>
                                            </span>
                                            <span class="timeline-time">
                                                <i class="fas fa-clock"></i> <?= isset($message['created_at']) ? date('H:i', strtotime($message['created_at'])) : '' ?>
                                            </span>
                                        </div>
                                    </div> 
                                </div> 
                                
                                <div class="message-subject-line">
                                     <span class="subject-label">Sujet:</span>
                                     <span class="message-subject"><?= htmlspecialchars($message['object'] ?? 'Aucun sujet') ?></span>
                                </div>
                                
                                <div class="message-content-card">
                                    <div class="message-content scrollable-content"> <!-- Scrollable content -->
                                        <?= nl2br(htmlspecialchars($message['content'] ?? 'Aucun contenu')) ?>
                                    </div>
                                </div>
                                
                                <div class="message-actions">
                                    <!-- Delete Form -->
                                    <form action="/carRental/controller/process_messages.php" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                        <input type="hidden" name="action" value="deletMessage">
                                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                        <button type="submit" class="btn btn-delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                   
                                </div>
                            </div> 
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div> 
                 </div> 
            </section> 

        </main> 
    </div> 
    
    <script src="/carRental/controller/scripts/admin.js"></script> 

</body>
</html>