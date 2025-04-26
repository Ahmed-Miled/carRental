<?php 
define('ROOT_PATH', __DIR__);
require 'views/includes/header.php';
require 'config/database.php';
require 'models/vehicule.php';

?>

<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Role Selection Screen -->
      <div class="modal-body" id="roleSelection">
        <p>Veuillez sélectionner votre rôle :</p>
        <div class="d-grid gap-2">
          <button class="btn btn-success role-btn" data-role="client">Client</button>
          <button class="btn btn-success role-btn" data-role="agency">Propriétaire d'agence</button>
        </div>
      </div>

      <!-- Login/Signup Forms (initially hidden) -->
      <div id="authForms" style="display:none;">
        <!-- Login Form -->
        <form id="loginForm" class="p-3">
          <div class="mb-3">
            <label for="loginEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="loginEmail" required>
          </div>
          <div class="mb-3">
            <label for="loginPassword" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="loginPassword" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Se connecter</button>
          <p class="text-center mt-2">
            <a href="#" class="toggle-auth" data-action="signup">Créer un compte</a>
          </p>
        </form>

        <!-- Signup Form -->
        <form id="signupForm" class="p-3" style="display:none;">
          <div class="mb-3">
            <label for="signupName" class="form-label">Nom complet</label>
            <input type="text" class="form-control" id="signupName" required>
          </div>
          <div class="mb-3">
            <label for="signupEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="signupEmail" required>
          </div>
          <div class="mb-3">
            <label for="signupPassword" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="signupPassword" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
          <p class="text-center mt-2">
            <a href="#" class="toggle-auth" data-action="login">Déjà un compte? Se connecter</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>


<div class="carousel-container">
  <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <div class="carousel-img" style="background-image: url('assets/img/nouveaute1.jpg');" role="img" aria-label="Nouveauté 1"></div>
      </div>
      <div class="carousel-item">
        <div class="carousel-img" style="background-image: url('assets/img/nouveaute2.jpg');" role="img" aria-label="Nouveauté 2"></div>
      </div>
      <div class="carousel-item">
        <div class="carousel-img" style="background-image: url('assets/img/nouveaute3.jpg');" role="img" aria-label="Nouveauté 3"></div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev" aria-label="Précédent">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next" aria-label="Suivant">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
  </div>
</div>


<section class="offers-section">
  <h2 class="section-title">Nos véhicules en promotion</h2>
  <p class="section-subtitle">Profitez de nos offres exceptionnelles sur une sélection de véhicules !</p>

  <div class="cards-container">
    
    <?php
    $promotions = getVehiculesEnPromotion($pdo);
    if (count($promotions) > 0):
        foreach ($promotions as $promotion):
        
          $vehicleData = htmlspecialchars(json_encode([
            'id' => $promotion['id'],
            'marque' => $promotion['marque'],
            'model' => $promotion['model'],
            'year' => $promotion['year'],
            'fuel_type' => $promotion['carburant'],
            'mileage' => number_format($promotion['kilometrage'], 0, ',', ' '),
            'description' => $promotion['boite_vitesse'],
            'image' => $promotion['image'],
            'price_per_day' => number_format($promotion['price_per_day'], 2, ',', ' '),
            'promotional_price' => number_format($promotion['promotional_price'], 2, ',', ' ')
        ]), ENT_QUOTES, 'UTF-8');


          echo "<script>console.log(" . json_encode($promotion) . ")</script>";
           // Formatage des prix
          $ancien_prix = number_format($promotion['price_per_day'], 2, ',', ' ');
          $nouveau_prix = number_format($promotion['promotional_price'], 2, ',', ' ');
    ?>
        <div class="vehicle" title="<?= htmlspecialchars($promotion['marque'] . ' ' . $promotion['model']) ?>">
            <img src="assets/img/<?= htmlspecialchars($promotion['image']) ?>" 
                 class="card-img" 
                 alt="<?= htmlspecialchars($promotion['marque'] . ' ' . $promotion['model']) ?>" 
                 loading="lazy">

            <div class="card-body">
                <h3><?= htmlspecialchars(ucfirst($promotion['marque']) . ' ' . htmlspecialchars(ucfirst($promotion['model']))) ?></h3>

                <div class="pricing">
                    <span class="old-price"><?= $ancien_prix ?> €</span>
                    <span class="new-price"><?= $nouveau_prix ?> €</span>
                </div>
        
    <a href="#" class="btn btn-details" 
    data-vehicle="<?= $vehicleData ?>" 
    title="Details">
    Détails
    </a>
            </div>
        </div>
    <?php
        endforeach;
    endif;
    ?>
  </div>

  <a href="views/recherchevehicules.php" class="btn btn-more" title="Voir plus">Voir plus de véhicules</a>
</section>

<!-- Vehicle Details Modal -->
<div id="vehicleModal" class="modal fade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="vehicleModalTitle">Vehicle Details</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="vehicleModalBody">
        <!-- Dynamic content will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-primary" id="bookNowBtn" style="background-color: #28a745;">Réserver maintenant</button>
      </div>
    </div>
  </div>
</div>


<div class="client-testimonials">
  <h2 class="section-title">Ce que disent nos clients</h2>
  
  <div class="testimonials-grid">
    <div class="testimonial-card">
      <div class="client-avatar">
        <img src="assets/img/personne1.jpg" alt="Photo de Tomas Lili" loading="lazy">
      </div>
      <blockquote>
        "Sed ut pers unde omnis iste natus error sit voluptatem accusantium dolor laudan rem aperiam, eaque ipsa quae ab illo inventore verit."
      </blockquote>
      <div class="client-info">
        <strong>Tomas Lili</strong>
        <span>New York</span>
      </div>
    </div>

    <div class="testimonial-card">
      <div class="client-avatar">
        <img src="assets/img/personne2.jpg" alt="Photo de Romi Rain" loading="lazy">
      </div>
      <blockquote>
        "Sed ut pers unde omnis iste natus error sit voluptatem accusantium dolor laudan rem aperiam, eaque ipsa quae ab illo inventore verit."
      </blockquote>
      <div class="client-info">
        <strong>Romi Rain</strong>
        <span>London</span>
      </div>
    </div>

    <div class="testimonial-card">
      <div class="client-avatar">
        <img src="assets/img/personne3.jpeg" alt="Photo de John Doe" loading="lazy">
      </div>
      <blockquote>
        "Sed ut pers unde omnis iste natus error sit voluptatem accusantium dolor laudan rem aperiam, eaque ipsa quae ab illo inventore verit."
      </blockquote>
      <div class="client-info">
        <strong>John Doe</strong>
        <span>Washington</span>
      </div>
    </div>
  </div>
</div>

<section class="map-section">
  <h2 class="section-title">Nous trouver</h2>
  <div class="map-container">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d18906.129712753736!2d6.722624160288201!3d60.12672284414915!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x463e997b1b6fc09d%3A0x6ee05405ec78a692!2sJ%C4%99zyk%20trola!5e0!3m2!1spl!2spl!4v1672239918130!5m2!1spl!2spl" 
      class="map-iframe"
      allowfullscreen="" 
      loading="lazy" 
      referrerpolicy="no-referrer-when-downgrade"
      title="Localisation de notre agence">
    </iframe>
  </div>
</section>


<?php 
require 'views/includes/footer.php';
?>

