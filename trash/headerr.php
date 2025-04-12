<?php 
session_start();
$logged_in = isset($_SESSION['logged_in']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Votre Titre</title>
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/carRental/assets/css/index.css">
  <link rel="stylesheet" href="/carRental/assets/css/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<header>
  <nav class="navv">
    <a href="/carRental/index.php" class="nom" title="CarRent">CarRent</a>
    
    <div class="search-container">
      <form id="searchForm" class="search-form">
        <div class="search-field">
          <input type="text" placeholder="Rechercher véhicules..." name="query" required>
          <button type="submit" title="Rechercher">
            <span>🔍</span>
          </button>
        </div>
        
        <select name="sort" class="sort-select">
          <option value="default">Trier par</option>
          <option value="price_asc">Prix (bas → haut)</option>
          <option value="price_desc">Prix (haut → bas)</option>
          <option value="voiture">Voitures seulement</option>
          <option value="velo">Vélos seulement</option>
        </select>
      </form>
    </div>
  
    <button class="menu-toggle" aria-label="Toggle menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
    <ul class="nav-links">
      <li><a href="/carRental/index.php" title="Accueil">Accueil</a></li>
      <li><a href="/carRental/views/contact.php" title="Contact">Contact</a></li>
      <li><a href="#page-bottom" title="À propos">À propos</a></li>
      <li><a class="connexion" href="/carRental/views/auth/auth.php" title="Connexion">Connexion</a></li>
    </ul>
  </nav>
  
  <!-- Profile Section -->
  <div class="profile-container <?php echo $logged_in ? 'visible' : 'hidden'; ?>">
    <?php if ($logged_in): ?>
      <div class="user-profile">
        <img src="assets/img/personne1.jpg" alt="Profile" id="profileImage" class="profile-image">
        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        
        <!-- Profile Dropdown Modal -->
        <div id="profileModal" class="modal">
          <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Account Management</h3>
            
            <div class="modal-actions">
              <button id="updateProfileBtn" class="btn-update">
                <i class="fas fa-edit"></i> Update Profile
              </button>
              
              <button id="deleteAccountBtn" class="btn-delete">
                <i class="fas fa-trash-alt"></i> Delete Account
              </button>
              
              <form action="/carRental/views/auth/logout.php" method="post" class="logout-form">
                <button type="submit" class="btn-logout">
                  <i class="fas fa-sign-out-alt"></i> Logout
                </button>
              </form>
            </div>
            
            <div id="updateForm" class="hidden">
              <form action="update_profile.php" method="post">
                <input type="text" name="new_username" placeholder="New Username" 
                       value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>">
                <input type="email" name="new_email" placeholder="New Email">
                <button type="submit">Save Changes</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div> 
</header>

<script src="/carRental/controller/scripts/profileModal.js"></script>
<script src="/carRental/controller/scripts/script.js"></script>