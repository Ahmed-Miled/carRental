<?php 

require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../../config/database.php';
require __DIR__ . '/../../models/client.php';


if (!isset($_SESSION['logged_in'])) {
    header('Location: /carRental/views/auth/logout.php');
    exit();
}

$rentals = getRentalHistory($pdo, $_SESSION['user_email']);

echo "<script>console.log(" . json_encode($rentals) . ");</script>";

?>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="/carRental/assets/css/clientDashBoard.css">

<div class="dashboard-container">
    
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
            <div class="dashboard-actions">
                <a href="/carRental/views/auth/logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Profile Section -->
            <div class="dashboard-card">
                <h2><i class="fas fa-user-circle"></i>Information du client</h2>
                <form action="/carRental/controller/updateProfile.php" method="POST">
                    <input type="hidden" name="action" value="updateClient">
                    <div class="mb-3">
                        <label class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" name="username" 
                               value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" disabled
                               value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">numéro de téléphone</label>
                        <input type="tel" class="form-control" name="phone" 
                               value="<?php echo htmlspecialchars($_SESSION['phoneNumber'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour le Profil
                    </button>
                </form>
            </div>

            <!-- Rental History -->
            <div class="dashboard-card">
                <h2><i class="fas fa-history"></i> Historique de location</h2>
                <?php if (empty($rentals)): ?>
                    <div class="alert alert-info">Aucun historique de location trouvé.</div>
                <?php else: ?>
                    <div class="rental-list">
                       
                        <?php foreach ($rentals as $rental): ?>
                            <div class="rental-item">
                                <img src="/carRental/assets/img/<?php echo htmlspecialchars($rental['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($rental['marque'].' '.$rental['model']); ?>">
                                <div class="rental-details">
                                    <h4><?php echo htmlspecialchars($rental['marque'].' '.$rental['model']); ?></h4>
                                    <div class="rental-meta">
                                        <span><i class="fas fa-calendar-alt"></i> <?php echo $rental['start_date']; ?></span>
                                        <span><i class="fas fa-dollar-sign"></i> <?php echo number_format($rental['price_per_day'], 2); ?></span>
                                        <span class="badge bg-<?php 
                                            echo $rental['status'] === 'available' ? 'success' : 
                                                 ($rental['status'] === 'pending' ? 'warning' : 'danger'); 
                                        ?>">
                                            <?php echo ucfirst($rental['status']); ?>
                                        </span>
                                        <span>
                                            <form action="/carRental/controller/manage_rental.php" method="post" class="stop-rental-form">
                                                <input type="hidden" name="action" value="stop_rental">
                                                <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars($rental['reservation_id']); ?>">
                                                <input type="hidden" name="car_id" value="<?php echo htmlspecialchars($rental['id']); ?>">
                                                <button type="submit" class="btn btn-sm btn-warning" title="Marquer cette location comme terminée">
                                                    <i class="fas fa-hand-paper"></i> Arrêter Location
                                                </button>
                                            </form>
                                        </span>
                                    </div>
                                </div>
                            </div>
                         <?php endforeach;  ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Account Management -->
            <div class="dashboard-card danger-zone">
                <h2><i id="warning-icon" class="fas fa-exclamation-triangle"></i>Gestion de Compte</h2>
                <div class="alert alert-warning">
                    <strong>Avertissement:</strong> Ces actions sont irréversibles.
                </div>
                
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt"></i> Supprimer Mon Compte
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
                <form id="deleteForm" action="/carRental/controller/updateProfile.php" method="POST">
                    <input type="hidden" name="action" value="deleteClientAccount">
                    <div class="mb-3">
                        <label class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" name="password" required>
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
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