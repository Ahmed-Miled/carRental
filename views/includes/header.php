<?php 
  session_start();
  $logged_in = isset($_SESSION['logged_in']);
  if ($logged_in){
    if ($_SESSION['role'] == 'client') {
    $role = 'client';
  }elseif ($_SESSION['role'] == 'agency') {
    $role = 'agency';
  }elseif ($_SESSION['role'] == NULL) {
    $role = 'visiteur';
  }
  }
  
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
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<header>
  <nav class="navv">
    <a href="/carRental/index.php" class="nom" title="CarRent">CarRent</a>
    
    <div class="search-container">
      
      <form id="searchForm" class="search-form" method="GET">
        <div class="search-field">
          <input type="text" id="globalSearchInput" placeholder="Rechercher véhicules..." name="search" required>
          <button type="submit" title="Rechercher">
            <span>🔍</span>
          </button>
        </div>
      </form>
    </div>
  
    <button class="menu-toggle" aria-label="Toggle menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
    <ul class="nav-links">
      <?php if ($logged_in && $role === 'client'): ?>
        <li><a href="/carRental/views/dash-board/clientDashBoard.php" >compte</a></li> 
      <?php elseif ($logged_in && $role === 'agency'): ?>
        <li><a href="/carRental/views/dash-board/agencyDashBoard.php" >compte</a></li>
      <?php endif; ?>
      <li><a href="/carRental/index.php"  title="Accueil" >Accueil</a></li>
      <?php if ($role !== 'agency'): ?>
      <li><a href="/carRental/views/recherchevehicules.php" title="Véhicules">Véhicules</a></li>
      <?php endif; ?>
      <li><a href="/carRental/views/contact.php" title="Contact">Contact</a></li>
      <li><a href="#page-bottom" title="À propos" >À propos</a></li>
      <?php if (!$logged_in): ?>
        <li><a class="connexion" href="/carRental/views/auth/authentification.php" title="Connexion">Connexion</a></li>
         
      <?php endif; ?>
    </ul>
   
  </nav>
  
</header>
<script>
// Script pour gérer la recherche globale
$(document).ready(function() {
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const searchQuery = $('#globalSearchInput').val().trim();
        
        if (searchQuery) {
            // Redirige vers la page de recherche avec le paramètre
            window.location.href = '/carRental/views/recherchevehicules.php?search=' + encodeURIComponent(searchQuery);
        }
    });
});
</script>