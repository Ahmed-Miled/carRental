<?php 
//require __DIR__ . '../views/includes/header.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/../models/vehicule.php';
require __DIR__ . '/../config/database.php';
$vehicules = getVehicule($pdo);
?>

<head>
    <title>Recherche Vehicule</title>
    <link rel="stylesheet" href="/carRental/assets/css/recherchevoiture.css">
</head>
<body>

<div class="container">
    <h1>TROUVEZ LE PRIX</h1>
    <h2>D'UNE VOITURE</h2>
    
    <div class="form-grid">
      <select>
        <option>Marque</option>
        <option>Audi</option>
        <option>Bmw</option>
        <option>Jaguar</option>
        <option>Mercedes-Benz</option>
        <option>Jeep</option>
        <option>Toyota</option>
        <option>Nissan</option>
        <option>Seat</option>
        <option>Golf</option>
      </select>
    </div>

    <div class="carrosserie-section">
    <div class="carrosserie-label">CARROSSERIE :</div>
    
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
    
    <div class="icon-row">
        <label class="icon-item">
            <input type="checkbox" name="carrosserie" value="hollandais" style="display: none;">
            <img src="../assets/img/hollandais.png" alt="Hollandais">
            <br>HOLLANDAIS
        </label>
        <label class="icon-item">
            <input type="checkbox" name="carrosserie" value="tout-chemin" style="display: none;">
            <img src="../assets/img/tout-chemin.png" alt="Tout chemin">
            <br>TOUT CHEMIN
        </label>
        <label class="icon-item">
            <input type="checkbox" name="carrosserie" value="velo-de-route" style="display: none;">
            <img src="../assets/img/velo-de-route.png" alt="Vélo de route">
            <br>ROUTE
        </label>
        <label class="icon-item">
            <input type="checkbox" name="carrosserie" value="velo-electrique" style="display: none;">
            <img src="../assets/img/velo-electrique.png" alt="Vélo électrique">
            <br>ELECTRIQUE
        </label>
    </div>
</div>
</div>

    <div class="form-grid">
      <select>
        <option>Voiture populaire</option>
        <option>..</option>
      </select>
      <select>
        <option>Nombre de places</option>
        <option>1</option>
        <option>2</option>
        <option>3</option>
        <option>4</option>
        <option>5</option>
        <option>6</option>
        <option>7</option>
        <option>8</option>
      </select>
      <select>
        <option>Nombre de portes</option>
        <option>2</option>
        <option>4</option>
        <option>6</option>
      </select>
      <select>
        <option>Nombre de cylindres</option>
        <option>2</option>
        <option>3</option>
        <option>4</option>
        <option>5</option>
        <option>6</option>
        <option>7</option>
        <option>8</option>
      </select>
      <select>
        <option>Énergie</option>
        <option>Essence</option>
        <option>Diesel</option>
        <option>Hybride</option>
        <option>Hybride Rechargeable</option>
        <option>Hybride Léger</option>
        <option>Electrique</option>
      </select>
      <select>
        <option>Boîte</option>
        <option>Manuelle</option>
        <option>Automatique</option>
        <option>Pilotée</option>
      </select>
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
            $agencId = $vehicule['agency_id'] ?? null;
            $imageFilename = $vehicule['image'] ?? 'default_car.png'; 
            $vehiculeId = $vehicule['id'] ?? '#'; 
            $kilometrage = $vehicule['kilometrage'] ?? 0;

            $agencyName = getAgencyName($pdo, $agencId);
            
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
          
                <a href="/carRental/views/reservation.php?id=<?= htmlspecialchars($vehiculeId) ?>" class="boutton" title="Réserver <?= htmlspecialchars($marque . ' ' . $model) ?>">Réserver</a>
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

<?php 
require __DIR__ . '/includes/footer.php';
?>