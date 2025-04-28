<?php
require __DIR__ . '/includes/header.php';
require __DIR__ . '/../models/vehicule.php';
require __DIR__ . '/../config/database.php';

// Configuration Python
define('PYTHON_SCRIPT', __DIR__ . '/../controller/scripts/indexation.py');

function searchVehicles($query) {
    try {
        $descriptors = [
            0 => ["pipe", "r"], // STDIN
            1 => ["pipe", "w"], // STDOUT
            2 => ["pipe", "w"]  // STDERR
        ];

        $process = proc_open(
            //escapeshellcmd('python ' . PYTHON_SCRIPT),
            //escapeshellcmd('/usr/bin/python3 ' . PYTHON_SCRIPT), // Chemin absolu
            escapeshellcmd('env LD_LIBRARY_PATH= /usr/bin/python3 ' . PYTHON_SCRIPT),

            $descriptors,
            $pipes,
            dirname(PYTHON_SCRIPT)
        );
        if (!is_resource($process)) {
            throw new Exception("Erreur: Impossible de démarrer le processus Python");
        }

        // Envoi de la requête
        fwrite($pipes[0], $query);
        fclose($pipes[0]);

        // Récupération des résultats
        $jsonOutput = stream_get_contents($pipes[1]);
        $errorOutput = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new Exception("Erreur Python: " . $errorOutput);
        }

        $result = json_decode($jsonOutput, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erreur: Format de réponse invalide");
        }

        return $result;

    } catch (Exception $e) {
        error_log("Erreur recherche: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}

function getVehiculesr($pdo, $vehiculesData) {
    $vehiculesComplets = [];
    
    if (!is_array($vehiculesData)) {
        error_log("Erreur: données véhicules invalides");
        return $vehiculesComplets;
    }

    foreach ($vehiculesData as $vehicule) {
        if (!isset($vehicule['id'])) {
            continue;
        }

        try {
            // Modified query with JOIN to get agency name
            $stmt = $pdo->prepare("
                SELECT cars.*, agency.fullName AS agency_name 
                FROM cars 
                LEFT JOIN agency ON cars.agency_id = agency.id 
                WHERE cars.id = ?
            ");
            $stmt->execute([$vehicule['id']]);
            $vehiculeComplet = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($vehiculeComplet) {
                // Add agency name to results
                echo "<script>console.log('Vehicule Complet:', " . json_encode($vehiculeComplet['agency_name']) . ");</script>";
                
                // Keep search score if exists
                if (isset($vehicule['score'])) {
                    $vehiculeComplet['score'] = $vehicule['score'];
                }
                $vehiculesComplets[] = $vehiculeComplet;
                $vehiculeComplet['agency_name'] = $vehiculeComplet['agency_name'] ?? 'Non spécifier';
            }
        } catch (PDOException $e) {
            error_log("Erreur PDO: " . $e->getMessage());
            continue;
        }
    }

    return $vehiculesComplets;
}

// Gestion de la recherche
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$vehicules = [];
$searchError = null;

if (!empty($searchQuery)) {
    $result = searchVehicles($searchQuery);
    if (isset($result['error'])) {
        $searchError = $result['error'];
    } elseif (isset($result['vehicules'])) {
        $vehicules = getVehiculesr($pdo, $result['vehicules']);
    }
} else {
    // Mode affichage de tous les véhicules
    $vehicules = getVehicules($pdo);
}
?>

<div class="container mt-5">
    <?php if (!empty($searchQuery)): ?>
        <h2 class="mb-4">
            <?php if (!empty($vehicules)): ?>
                <?= count($vehicules) ?> résultat(s) pour "<?= htmlspecialchars($searchQuery) ?>"
            <?php else: ?>
                Aucun résultat pour "<?= htmlspecialchars($searchQuery) ?>"
            <?php endif; ?>
        </h2>
    <?php else: ?>
        <h2 class="mb-4">Tous nos véhicules disponibles</h2>
    <?php endif; ?>

    <?php if ($searchError): ?>
        <div class="alert alert-danger mb-4">
            <?= htmlspecialchars($searchError) ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if (!empty($vehicules)): ?>
            <?php foreach ($vehicules as $vehicule): ?>
            
                <?php 
                    echo "<script>console.log('lena laezm tal9a el id mta3 el agence');</script>"; 
                    echo "<script>console.log('Vehicule:', " . json_encode($vehicule) . ");</script>"
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="/carRental/assets/img/<?= htmlspecialchars($vehicule['image'] ?? 'default-car.jpg') ?>" 
                             class="card-img-top vehicle-img"
                             alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['model']) ?>">
                        
                        <?php if (isset($vehicule['score'])): ?>
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-success">
                                    <?= round($vehicule['score'] * 100) ?>% match
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title">
                                <?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['model']) ?>
                            </h5>
                            <div class="vehicle-specs">
                                <p><i class="fas fa-calendar-alt"></i> Année: <?= $vehicule['year'] ?? 'N/A' ?></p>
                                <p><i class="fas fa-tachometer-alt"></i> KM: <?= number_format($vehicule['kilometrage'] ?? 0, 0, '', ' ') ?></p>
                                <p><i class="fas fa-gas-pump"></i> Carburant: <?= htmlspecialchars($vehicule['carburant'] ?? 'N/A') ?></p>
                                <?php echo "<script>console.log('nbr place:', " . json_encode($vehicule) . ");</script>" ?>
                                <p><i class="fas fa-user-tie"></i> Propriétaire: <?= htmlspecialchars($vehicule['agency_name'] ?? 'Propriétaire non renseigné') ?></p>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-success mb-0">
                                    <?= number_format($vehicule['price_per_day'] ?? 0, 2, ',', ' ') ?> DT/jour
                                </span>
                                <a href="/carRental/views/reservation.php?id=<?= $vehicule['id'] ?>&agency_id=<?= htmlspecialchars($vehicule['agency_id']) ?>" class="btn btn-success">
                                
                                    <i class="fas fa-car"></i> Réserver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h3>Aucun véhicule disponible</h3>
                    <p class="mb-0">Essayez avec d'autres critères de recherche</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.vehicle-img {
    height: 200px;
    object-fit: cover;
}
.vehicle-specs p {
    margin-bottom: 0.5rem;
}
.row{    
    margin-bottom: 4rem;
}
</style>

<?php require __DIR__ . '/includes/footer.php'; ?>