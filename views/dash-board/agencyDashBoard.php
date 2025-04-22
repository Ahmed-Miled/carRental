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
            <h1>Welcome, <?php echo htmlspecialchars($agency['name']); ?></h1>
            <div class="dashboard-actions">
                <a href="/carRental/views/auth/logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Collapsible Agency Information -->
            <div class="dashboard-card">
                <div class="card-header toggle-section" data-target="agency-info">
                    <h2>
                        <i class="fas fa-building"></i> Agency Information
                        <i class="fas fa-chevron-down toggle-icon float-end"></i>
                    </h2>
                </div>
                <div id="agency-info" class="card-body collapse">
                    <form action="/carRental/controllers/updateAgencyProfile.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Agency Name</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?php echo htmlspecialchars($agency['name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($agency['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" 
                                   value="<?php echo htmlspecialchars($agency['phoneNumber']); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" 
                                   value="<?php echo htmlspecialchars($agency['address']); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <!-- Add New Vehicle with Plus Icon -->
            <div class="dashboard-card">
                <div class="card-header toggle-section" data-target="add-vehicle">
                    <h2>
                        <i class="fas fa-plus toggle-icon"></i>
                        Add New Vehicle
                        <i class="fas fa-chevron-down toggle-icon float-end"></i>
                    </h2>
                </div>
                <!--
                <div id="add-vehicle" class="card-body collapse">
                    <form action="/carRental/controller/addVehicle.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Vehicle Type</label>
                            <select class="form-select" name="type" required>
                                <option value="car">Car</option>
                                <option value="bike">Bike</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Brand</label>
                            <input type="text" class="form-control" name="brand" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <input type="text" class="form-control" name="model" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year</label>
                            <input type="number" class="form-control" name="year" min="1900" max="<?php echo date('Y')+1; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Daily Rate ($)</label>
                            <input type="number" class="form-control" name="daily_rate" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vehicle Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Add Vehicle
                        </button>
                    </form>
                </div>
                -->

                <div id="add-vehicle" class="card-body collapse">
    <form action="/carRental/controller/addVehicle.php" method="POST" enctype="multipart/form-data">
        
        <input type="hidden" name="agency_id" value="<?= $agency['id'] ?>">
        <div class="mb-3">
            <label class="form-label">Marque</label>
            <input type="text" class="form-control" name="marque" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Model</label>
            <input type="text" class="form-control" name="model" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kilometrage</label>
            <input type="number" class="form-control" name="kilometrage" min="0" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Année</label>
            <input type="number" class="form-control" name="year" min="1900" max="<?php echo date('Y')+1; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Prix Par Jour ($)</label>
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
            <label class="form-label">Fuel Type (Carburant)</label>
            <select class="form-select" name="carburant" required>
                <option value="essence">Essance</option>
                <option value="diesel">Diesel</option>
                <option value="hybride">Hybrid</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Number of Seats (nbr_place)</label>
            <input type="number" class="form-control" name="nbr_place" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nombre des cylinders</label>
            <input type="number" class="form-control" name="nbr_cylindres" min="1" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Boite vitesse</label>
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
                <h2><i class="fas fa-warehouse"></i> Vehicle Inventory</h2>
                <?php if (empty($vehiclesInventory)): ?>
                    <div class="alert alert-info">No vehicles in inventory.</div>
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
                                        <span><i class="fas fa-tag"></i> <?php echo ucfirst($vehicle['type']); ?></span>
                                        <span><i class="fas fa-dollar-sign"></i> <?php echo number_format($vehicle['price_per_day'], 2); ?>/day</span>
                                        <span class="badge bg-<?php echo $vehicle['available'] ? 'success' : 'danger'; ?>">
                                            <?php echo $vehicle['available'] ? 'Available' : 'Rented'; ?>
                                        </span>
                                    </div>
                                    <div class="vehicle-actions">
                                        <button class="btn btn-sm btn-outline-primary edit-vehicle" 
                                                data-id="<?php echo $vehicle['id']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-vehicle" 
                                                data-id="<?php echo $vehicle['id']; ?>">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Rental Requests -->
            <div class="dashboard-card">
                <h2><i class="fas fa-list-alt"></i> Rental Requests</h2>
                <?php if (empty($rentals)): ?>
                    <div class="alert alert-info">No rental requests found.</div>
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
                                            <td>$<?php echo number_format($rental['total_cost'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $rental['status'] === 'approved' ? 'success' : 
                                                         ($rental['status'] === 'pending' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?php echo ucfirst($rental['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($rental['status'] === 'pending'): ?>
                                                    <form action="/carRental/controller/process_rental_request.php" method="post">
                                                        <input type="hidden" name="action" value="approve">
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
                <h2><i class="fas fa-exclamation-triangle"></i> Account Management</h2>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> These actions are irreversible.
                </div>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash-alt"></i> Delete Agency Account
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
                <p>This will permanently delete your agency account and all associated data. This action cannot be undone.</p>
                <form id="deleteForm" action="/carRental/controllers/deleteAgencyAccount.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" name="password" required>
                        <input type="hidden" name="agency_id" value="<?php echo $agency_id; ?>">
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

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Vehicle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
            fetch(`/carRental/controllers/getVehicle.php?id=${vehicleId}`)
                .then(response => response.text())
                .then(html => {
                    document.querySelector('#editVehicleModal .modal-body').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('editVehicleModal')).show();
                });
        });
    });

    // 3. Delete Vehicle Button
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