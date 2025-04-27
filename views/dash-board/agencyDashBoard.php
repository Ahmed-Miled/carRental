<?php 
require __DIR__ . '/../includes/header.php';

// Check if agency is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'agency') {
    header('Location: /carRental/views/auth/authentification.php');
    exit();
}

// Database connection
require __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/vehicule.php';
require __DIR__ . '/../../controller/process_rental_request.php';


$agency['id'] = $_SESSION['user_id'];
$agency['name'] = $_SESSION['user_name'];
$agency['email'] = $_SESSION['user_email'];
$agency['phoneNumber'] = $_SESSION['phoneNumber'];
$agency['address'] = $_SESSION['address'];
$agency['created_at'] = $_SESSION['created_at'];

$rentals = getRentalRequests($pdo, $agency['id']);
$vehiclesInventory = getAgencyVehicles($pdo, $agency['id']);
echo "<script>console.log('rental request');</script>";
echo "<script>console.log(" . json_encode($rentals) . ");</script>";
echo "<script>console.log('vh inventory');</script>";
echo "<script>console.log(" . json_encode($vehiclesInventory) . ");</script>";


/*
// Fetch rental requests
$rentals_stmt = $pdo->prepare("
    SELECT r.*, v.model, v.brand, u.name as client_name
    FROM rentals r
    JOIN vehicles v ON r.vehicle_id = v.id
    JOIN users u ON r.user_id = u.id
    WHERE v.agency_id = ?
    ORDER BY r.rental_date DESC
");
$rentals_stmt->execute([$agency_id]);
$rentals = $rentals_stmt->fetchAll();
*/
?>

<link rel="stylesheet" href="/carRental/assets/css/agencyDashBoard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="dashboard-container">
    <div class="main-content">
        <div class="dashboard-header">
            <h1>Bienvenue, <?php echo htmlspecialchars($agency['name']); ?></h1>
            <div class="dashboard-actions">
                <a href="/carRental/views/auth/logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Collapsible Agency Information -->
            <div class="dashboard-card">
                <div class="card-header toggle-section" data-target="agency-info">
                    <h2>
                        <i class="fas fa-building"></i> Informations sur l'agence
                        <i class="fas fa-chevron-down toggle-icon float-end"></i>
                    </h2>
                </div>
                <div id="agency-info" class="card-body collapse">
                    <form action="/carRental/controller/updateProfile.php" method="POST">
                        <input type="hidden" name="action" value="updateAgency" >
                        <div class="mb-3">
                            <label class="form-label">Nom de l'agence</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?php echo htmlspecialchars($agency['name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($agency['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">numéro de téléphone</label>
                            <input type="tel" class="form-control" name="phone" 
                                   value="<?php echo htmlspecialchars($agency['phoneNumber']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">emplacement</label>
                            <input type="text" class="form-control" name="address" 
                                   value="<?php echo htmlspecialchars($agency['address']); ?>">
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Mettre à jour le profil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Add New Vehicle with Plus Icon -->
            <div class="dashboard-card">
                <div class="card-header toggle-section" data-target="add-vehicle">
                    <h2>
                        <i class="fas fa-plus toggle-icon"></i>
                        Ajouter un nouveau véhicule
                        <i class="fas fa-chevron-down toggle-icon float-end"></i>
                    </h2>
                </div>

                <div id="add-vehicle" class="card-body collapse">
                    <form action="/carRental/controller/manipuleVehicle.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">

                        <input type="hidden" name="agency_id" value="<?= $agency['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Marque</label>
                            <input type="text" class="form-control" name="marque" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Modèle</label>
                            <input type="text" class="form-control" name="model" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kilométrage</label>
                            <input type="number" class="form-control" name="kilometrage" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Année</label>
                            <input type="number" class="form-control" name="year" min="1900" max="<?php echo date('Y')+1; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prix Par Jour (DT)</label>
                            <input type="number" class="form-control" name="price_per_day" step="0.01" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vehicle Image</label>
                            <select class="form-select" name="image" required>
                                <option value="offer1.png">Kia rio</option>
                                <option value="offer2.png">dacia sandro</option>
                                <option value="offer3.png">Seat ibiza</option>
                                <option value="offer4.png">I20</option>
                                <option value="BMW_Série_1.jpg">Bmw serie 1</option>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type de carburant</label>
                            <select class="form-select" name="carburant" required>
                                <option value="essence">Essance</option>
                                <option value="diesel">Diesel</option>
                                <option value="hybride">Hybrid</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre de sièges</label>
                            <input type="number" class="form-control" name="nbr_place" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre de cylindres</label>
                            <input type="number" class="form-control" name="nbr_cylindres" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Boîte de vitesse</label>
                            <select class="form-select" name="boite_vitesse" required>
                                <option value="manuelle">Manuelle</option>
                                <option value="automatique">Automatique</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Ajoute Vehicle
                        </button>
                    </form>
                </div>
            </div>

            <!-- Vehicle Inventory -->
            <div class="dashboard-card">
                <h2><i class="fas fa-warehouse"></i> Inventaire des véhicules</h2>
                <?php if (empty($vehiclesInventory)): ?>
                    <div class="alert alert-info">Aucun véhicule en inventaire.</div>
                <?php else: ?>
                    <div class="vehicle-list">
                        <?php foreach ($vehiclesInventory as $vehicle): ?>
                            <div class="vehicle-item">
                                <div class="vehicle-image">
                                    <img src="/carRental/assets/img/<?php echo htmlspecialchars($vehicle['image'] ?: 'default-vehicle.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($vehicle['model']); ?>">
                                </div>
                                <div class="vehicle-details">
                                    <h4><?php echo htmlspecialchars($vehicle['model'].' ('.$vehicle['year'].')'.' id : '.$vehicle['id']); ?></h4>
                                    <div class="vehicle-meta">
                                        <!--
                                        <span><i class="fas fa-tag"></i> <?php echo ucfirst($vehicle['type']); ?></span>
                                        -->
                                        <span><i class="fas fa-dollar-sign"></i> <?php echo number_format($vehicle['price_per_day'], 2); ?>/day</span>
                                        
                                        <span class="badge bg-<?php 
                                                    echo $vehicle['status'] === 'available' ? 'success' : 
                                                         ($vehicle['status'] === 'pending' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?php echo ucfirst($vehicle['status']);  ?>
                                                </span>
                                    </div>
                                    <div class="vehicle-actions">
                                        
                                            <button class="btn btn-sm btn-outline-success edit-vehicle" 
                                                    data-id="<?php echo $vehicle['id']; ?>">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>

                                            <!-- Inside the vehicle inventory loop -->
                                            <button class="btn btn-sm btn-outline-info add-promotion"
                                                    data-bs-toggle="modal"  
                                                    data-bs-target="#promotionModal" 
                                                    data-id="<?php echo $vehicle['id']; ?>"
                                                    data-price="<?php echo $vehicle['price_per_day']; ?>">
                                                <i class="fas fa-tag"></i> Ajouter une promotion
                                            </button>
                                        
                                        
                                        <form action="/carRental/controller/manipuleVehicle.php" method="post">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="car_id" value="<?= $vehicle['id']; ?>">
                                            <input type="hidden" name="agency_id" value="<?= $agency['id']; ?>">
                                            <button class="btn btn-sm btn-outline-danger delete-vehicle" 
                                                    data-id="<?php echo $vehicle['id']; ?>">
                                                <i class="fas fa-trash-alt"></i> Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Rental Requests -->
            <div class="dashboard-card">
                <h2><i class="fas fa-list-alt"></i>Demandes de location</h2>
                <?php if (empty($rentals)): ?>
                    <div class="alert alert-info">Aucune demande de location trouvée.</div>
                <?php else: ?>
                    <div class="rental-requests">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Vehicle id</th>
                                        <th>Dates</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rentals as $rental): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($rental['clientName']); ?></td>
                                            <td><?php echo htmlspecialchars($rental['vehicule_id']); ?></td>
                                            <td>
                                                <?php echo date('M j', strtotime($rental['start_date'])); ?> - 
                                                <?php echo date('M j, Y', strtotime($rental['end_date'])); ?>
                                            </td>
                                            <td><?php echo $rental['prix_total']; ?> DT</td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $rental['status'] === 'approved' ? 'success' : 
                                                         ($rental['status'] === 'pending' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?php echo ucfirst($rental['status']);  ?>
                                                </span>
                                    
                                            </td>
                                            <td class="azerty">
                                                <?php if ($rental['status'] === 'pending'): ?>
                                                    <form action="/carRental/controller/process_rental_request.php" method="post">
                                                        <input type="hidden" name="action" value="approve">
                                                        <input type="hidden" name="start_date" value="<?= $rental['start_date'] ?>">
                                                        <input type="hidden" name="end_date" value="<?= $rental['end_date'] ?>">
                                                        <input type="hidden" name="vehicule_id" value="<?= $rental['vehicule_id'] ?>">
                                                        <input type="hidden" name="numDemande" value="<?= $rental['id'] ?>">
                                                        <input type="hidden" name="vehicule_id" value="<?= $rental['vehicule_id'] ?>">
                                                        <button class="btn btn-sm btn-success approve-rental" 
                                                                data-id="<?php echo $rental['id']; ?>">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form action="/carRental/controller/process_rental_request.php" method="post">
                                                        <input type="hidden" name="action" value="reject">
                                                        <input type="hidden" name="numDemande" value="<?= $rental['id'] ?>">
                                                        <input type="hidden" name="vehicule_id" value="<?= $rental['vehicule_id'] ?>">
                                                        <button class="btn btn-sm btn-danger reject-rental" 
                                                                data-id="<?php echo $rental['id']; ?>">
                                                            Reject
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Account Management -->
            <div class="dashboard-card danger-zone">
                <h2><i class="fas fa-exclamation-triangle"></i> Gestion de compte</h2>
                <div class="alert alert-warning">
                    <strong>Avertissement:</strong> Ces actions sont irréversibles.
                </div>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt"></i> Supprimer le compte de l'agence
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Modifier le véhicule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Promotion Modal -->
<form action="/carRental/controller/manipuleVehicle.php" method="post">
    <input type="hidden" name="action" value="addPromotion">
    <div class="modal fade" id="promotionModal" tabindex="-1" role="dialog" aria-labelledby="promotionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="promotionModalLabel">Ajouter une promotion</h5>
                        <!-- Use Bootstrap 5 standard close button structure -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                                                    
                    <div class="modal-body">
                        <input type="hidden" name="vehicle_id" id="promotionVehicleId">
                        <input type="hidden" name="agency_id" value="<?= $agency['id']; ?>">
                                                    
                        <div class="form-group">
                            <label>Prix ​​actuel</label>
                            <input type="number" class="form-control" name="current_price" id="currentPrice" disabled>
                        </div>
                                                    
                        <div class="form-group">
                            <label for="newPrice">Prix ​​promotionnel</label>
                            <input type="number" step="0.01" class="form-control" name="new_price" required>
                        </div>
                                                    
                        <div class="form-group">
                            <label for="endDate">Date de fin de la promotion</label>
                            <input type="date" class="form-control" name="end_date" required min="<?= date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>      
                        <button type="submit" class="btn btn-primary">Enregistrer la promotion</button>
                    </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {

    $('#promotionModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const vehicleId = button.data('id');
        const currentPrice = button.data('price');
        
        const modal = $(this);
        modal.find('#promotionVehicleId').val(vehicleId);
        modal.find('#currentPrice').val(currentPrice);
    });

    // This runs when the page finishes loading
    
    // 1. Toggle Sections
    document.querySelectorAll('.toggle-section').forEach(function(element) {
        element.addEventListener('click', function() {
            const target = this.dataset.target;
            const targetElement = document.getElementById(target);
            
            // Toggle collapse
            targetElement.classList.toggle('show');
            
            // Toggle icons
            this.querySelectorAll('.toggle-icon').forEach(function(icon) {
                // Chevron icons
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
                
                // Plus/Minus icons
                if (icon.classList.contains('fa-plus')) {
                    icon.classList.toggle('fa-plus');
                    icon.classList.toggle('fa-minus');
                }
            });
        });
    });
    

    // 2. Edit Vehicle Button
    document.querySelectorAll('.edit-vehicle').forEach(function(button) {
        button.addEventListener('click', function() {
            const vehicleId = this.dataset.id;
            fetch(`/carRental/controller/manipuleVehicle.php?action=getCar&id=${vehicleId}`)
            .then(response => response.text())
            .then(html => {
                document.querySelector('#editVehicleModal .modal-body').innerHTML = html;
                new bootstrap.Modal(document.getElementById('editVehicleModal')).show();
            });
        });
    });

    // 3. Delete Vehicle Button
    /*
    document.querySelectorAll('.delete-vehicle').forEach(function(button) {
        button.addEventListener('click', function() {
            if (confirm('Are you sure?')) {
                const vehicleId = this.dataset.id;
                fetch('/carRental/controllers/deleteVehicle.php', {
                    method: 'POST',
                    body: JSON.stringify({ id: vehicleId }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        });
    });
    */
    // 4. Rental Status Buttons
    function updateRentalStatus(status) {
        return function() {
            const rentalId = this.dataset.id;
            fetch('/carRental/controllers/updateRentalStatus.php', {
                method: 'POST',
                body: JSON.stringify({
                    id: rentalId,
                    status: status
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    document.querySelectorAll('.approve-rental').forEach(function(button) {
        button.addEventListener('click', updateRentalStatus('approved'));
    });

    document.querySelectorAll('.reject-rental').forEach(function(button) {
        button.addEventListener('click', updateRentalStatus('rejected'));
    });
});
</script>

<?php 
require __DIR__ . '/../includes/footer.php';  
?>