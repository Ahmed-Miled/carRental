<?php 
require 'views/includes/header.php';
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
        <div class="carousel-img" style="background-image: url('assets/img/v1.jpeg');" role="img" aria-label="Véhicule 1"></div>
      </div>
      <div class="carousel-item">
        <div class="carousel-img" style="background-image: url('assets/img/nouveaute2.jpg');" role="img" aria-label="Nouveauté 2"></div>
      </div>
      <div class="carousel-item">
        <div class="carousel-img" style="background-image: url('assets/img/v2.avif');" role="img" aria-label="Véhicule 2"></div>
      </div>
      <div class="carousel-item">
        <div class="carousel-img" style="background-image: url('assets/img/nouveaute3.jpg');" role="img" aria-label="Nouveauté 3"></div>
      </div>
      <div class="carousel-item">
        <div class="carousel-img" style="background-image: url('assets/img/v3.jpg');" role="img" aria-label="Véhicule 3"></div>
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

  <button class="btn btn-more" title="Voir plus" role="button">Voir plus de véhicules</button>
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


<section class="offers-section">
  <h2 class="section-title">Nos vélos en promotion</h2>
  <p class="section-subtitle">Profitez de nos offres exceptionnelles sur une sélection de vélos !</p>

  <div class="cards-container">
    <div class="vehicle velo" title="VÉLO BEST BIKE 26">
      <img src="assets/img/v4.jpg" class="card-img" alt="VÉLO BEST BIKE 26" loading="lazy">
      <div class="card-body">
        <h3>VÉLO BEST BIKE 26</h3>
        <p>La bicyclette Best Bike 26" est construite autour d'un cadre en Platinum, offrant ainsi durabilité et résistance.</p>
        <a href="#" class="btn btn-details" title="Details" role="button">Détails</a>
      </div>
    </div>

    <div class="vehicle velo" title="TRQZNLEP Vélo">
      <img src="assets/img/v5.avif" class="card-img" alt="TRQZNLEP Vélo" loading="lazy">
      <div class="card-body">
        <h3>TRQZNLEP Vélo</h3>
        <p>Transformez Facilement Votre Vélo En Vélo Électrique</p>
        <a href="#" class="btn btn-details" title="Details" role="button">Détails</a>
      </div>
    </div>

    <div class="vehicle velo" title="adulte tout-terrain">
      <img src="assets/img/v6.avif" class="card-img" alt="adulte tout-terrain" loading="lazy">
      <div class="card-body">
        <h3>Adulte tout-terrain</h3>
        <p>vélo de route hommes femmes 26 pouces couteau roue vélo à vitesse Variable vtt vélo.</p>
        <a href="#" class="btn btn-details" title="Details" role="button">Détails</a>
      </div>
    </div>
  
    <div class="vehicle velo" title="Vélos de Ville">
      <img src="assets/img/v7.avif" class="card-img" alt="Vélos de Ville" loading="lazy">
      <div class="card-body">
        <h3>Vélos de Ville</h3>
        <p>Évadez-vous en ville grâce à nos vélos urbains (hollandais, fixie, longues distances), vélos électriques confortables, sûrs et équipés !</p>
        <a href="#" class="btn btn-details" title="Details" role="button">Détails</a>
      </div>
    </div>  
  </div>

  <button class="btn btn-more" title="Voir plus" role="button">Voir plus de vélos</button>
</section>

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