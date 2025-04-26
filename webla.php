<!--
<section class="offers-section">
  <h2 class="section-title">Nos véhicules en promotion</h2>
  <p class="section-subtitle">Profitez de nos offres exceptionnelles sur une sélection de véhicules !</p>

  <div class="cards-container">
    <div class="vehicle" title="Dacia Sandero Stepway">
      <img src="assets/img/offer2.png" class="card-img" alt="Dacia Sandero Stepway" loading="lazy">
      <div class="card-body">
        <h3>Dacia Sandero Stepway</h3>
        <p>Crossover urbain avec excellent rapport qualité-prix.</p>
        <a href="#" class="btn btn-details" data-id="1" title="Details">Détails</a>
      </div>
    </div>

    <div class="vehicle" title="Seat Ibiza">
      <img src="assets/img/offer3.png" class="card-img" alt="Seat Ibiza" loading="lazy">
      <div class="card-body">
        <h3>Seat Ibiza</h3>
        <p>Citadine sportive avec technologies dernières générations.</p>
        <a href="#" class="btn btn-details" data-id="2" title="Details">Détails</a>

      </div>
    </div>

    <div class="vehicle" title="Hyundai i20">
      <img src="assets/img/offer4.png" class="card-img" alt="Hyundai i20" loading="lazy">
      <div class="card-body">
        <h3>Hyundai i20</h3>
        <p>Citadine moderne alliant design élégant et confort optimal.</p>
        <a href="#" class="btn btn-details" data-id="3" title="Details">Détails</a>

      </div>
    </div>
  
    <div class="vehicle" title="Swift Auto">
      <img src="assets/img/car5.webp" class="card-img" alt="Swift Auto" loading="lazy">
      <div class="card-body">
        <h3>Swift Auto</h3>
        <p>Son profil abaissé et élargi, associé à des lignes dynamiques et sportives, attire tous les regards.</p>
        <a class="btn btn-details" data-id="4" title="Details">Détails</a>

      </div>
    </div>
  </div>
  <a href="views/recherchevehicules.php" class="btn btn-more" title="Voir plus">Voir plus de vélos</a>
  
</section>
-->



<?php/*
    try {
        // Get 4 random active promotions
        
        $promotions = getVehiculesEnPromotion($pdo);

        if (count($promotions) > 0) {
            foreach ($promotions as $promotion) {
                // Calculate discount percentage
                $discount = round(($promotion['original_price'] - $promotion['promotional_price']) / $promotion['original_price'] * 100);
                echo '<script>console.log(' . json_encode($discount) . ')</script>';
                echo '
                <div class="vehicle" title="'.htmlspecialchars($promotion['vehicle_name']).'">
                    <img src="'.htmlspecialchars($promotion['image_path']).'" class="card-img" alt="'.htmlspecialchars($promotion['vehicle_name']).'" loading="lazy">
                    <div class="card-body">
                        <h3>'.htmlspecialchars($promotion['vehicle_name']).'</h3>
                        <div class="price-container">
                            <span class="original-price">'.number_format($promotion['original_price'], 2, ',', ' ').' €</span>
                            <span class="promotional-price">'.number_format($promotion['promotional_price'], 2, ',', ' ').' €</span>
                            <span class="discount-badge">-'.$discount.'%</span>
                        </div>
                        <p>'.htmlspecialchars($promotion['description']).'</p>
                        <a href="vehicle_details.php?id='.$promotion['vehicle_id'].'" class="btn btn-details" data-id="'.$promotion['id'].'" title="Details">Détails</a>
                    </div>
                </div>';
            }
        } else {
            echo '<p>Aucune promotion disponible pour le moment.</p>';
        }
    } catch (PDOException $e) {
        echo '<p class="error">Erreur de chargement des promotions : '.$e->getMessage().'</p>';
    }
*/?>



<?php
  $promotions = getVehiculesEnPromotion($pdo);

  if (count($promotions) > 0):
    foreach ($promotions as $promotion):
?>
      <div class="vehicle" title="Seat Ibiza">
      <img src="assets/img/<?= $promotion['image'] ?>" class="card-img" alt="Seat Ibiza" loading="lazy">
      <div class="card-body">
        <h3><?= $promotion['marque'] . ' ' . $promotion['model'] ?></h3>
        <p><?= $promotion['nbr_cylindres'] . ' cylinders avec un mouteur ' . $promotion['carburant'] . ' et une boite de vitesse ' . $promotion['boite_vitesse'] ?></p>
        <a href="#" class="btn btn-details" data-id="2" title="Details">Détails</a>

      </div>
    </div>
<?php
      endforeach;
  endif;
?>
