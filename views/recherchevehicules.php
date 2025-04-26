<?php 
//require __DIR__ . '../views/includes/header.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/../models/vehicule.php';
require __DIR__ . '/../config/database.php';
$vehicules = getVehicules($pdo);
?>

<head>
    <title>Recherche Vehicule</title>
    <link rel="stylesheet" href="/carRental/assets/css/recherchevoiture.css">
</head>

<div class="rechercheContainer">
<div class="container">
    <h1>TROUVEZ LE PRIX</h1>
    <h2>D'UNE VOITURE</h2>

    
    <div class="carrosserie-icons">
        <div class="icon-row">
        <label class="icon-item">
            <input type="checkbox" name="carrosserie" value="berline" style="display: none;">
            <img src="../assets/img/berline.png" alt="Berline">
            <br>BERLINE
        </label>
        <label class="icon-item">
            <input type="checkbox" name="carrosserie" value="suv" style="display: none;">
            <img src="../assets/img/suv.png" alt="SUV">
            <br>SUV
        </label>
        <label class="icon-item">
            <input type="checkbox" name="carrosserie" value="coupe" style="display: none;">
            <img src="../assets/img/coupe.png" alt="Coupé">
            <br>COUPÉ
        </label>
        <label class="icon-item">
            <input type="checkbox" name="carrosserie" value="utilitaire" style="display: none;">
            <img src="../assets/img/UTILITAIRE.png" alt="Utilitaire">
            <br>UTILITAIRE
        </label>
    </div>
    
</div>
</div>

    <button class="rech">Rechercher</button>

    <!-- affichage dynamique -->


<?php 
    if (isset($vehicules) && is_array($vehicules) && !empty($vehicules)):
    
        foreach ($vehicules as $vehicule):
            // --- Provide default values in case keys are missing ---
            $marque = $vehicule['marque'] ?? 'Marque inconnue';
            $model = $vehicule['model'] ?? 'Modèle inconnu';
            $year = $vehicule['year'] ?? 'anne inconnue';
            $prixParJour = $vehicule['price_per_day'] ?? 0;
            $agency_id = $vehicule['agency_id'] ?? null;
            $imageFilename = $vehicule['image'] ?? 'default_car.png'; 
            $vehiculeId = $vehicule['id'] ?? '#'; 
            $kilometrage = $vehicule['kilometrage'] ?? 0;

            $agencyName = getAgencyName($pdo, $agency_id);
            
            // --- Determine Agency display text ---
            $agencyDisplayText = 'Agence inconnue'; 
            if (!empty($agencyName)) {
                $agencyDisplayText = $agencyName; 
            } elseif (!empty($agencId)) {
                $agencyDisplayText = 'Agence ID: ' . $agencId; 
            }
          
            
            $imagePath = '../assets/img/' . $imageFilename;
            $formattedPrice = number_format((float)$prixParJour, 2, ',', ' ') . ' DT/Jour'; // Format with 2 decimals, comma separator
          
    ?>
        <div class="vehicule">
            <!-- Dynamic Image Source and Alt Text -->
            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($marque . ' ' . $model) ?>" onerror="this.onerror=null; this.src='../assets/img/default_car.png';">
            <!-- Add onerror fallback for broken image links -->
            
            <div class="vehicule-contenu">
                
                <div class="titre"><?= htmlspecialchars($marque) ?> <?= htmlspecialchars($model) ?></div>
          
                <div class="year"><?= htmlspecialchars($year) ?></div>
          
                <div class="details"> <?= htmlspecialchars ($kilometrage) ?>Km</div>

                <div class="prix"><?= htmlspecialchars($formattedPrice) ?></div>
          
                <div class="concessionnaire"><?= htmlspecialchars($agencyDisplayText) ?></div>
          
                <a href="/carRental/views/reservation.php?id=<?= htmlspecialchars($vehiculeId) ?>&agency_id=<?= htmlspecialchars($agency_id) ?>" class="boutton" title="Réserver <?= htmlspecialchars($marque . ' ' . $model) ?>">Réserver</a>
                <!-- Adjust href="reservation.php..." to your actual reservation page -->
            </div>
        </div>
    <?php
        endforeach; // End the loop for each vehicle
    else: // If $vehicules is not set, not an array, or empty
    ?>
        <p>Aucun véhicule disponible pour le moment.</p> <!-- Or any other message -->
    <?php endif; ?>

  
</div>
</div>
<?php 
require __DIR__ . '/includes/footer.php';
?>