<?php 
//require __DIR__ . '../views/includes/header.php';
require __DIR__ . '/includes/header.php';
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

    <!-- Nouvelle section pour l'affichage du véhicule -->
    <div class="vehicule">
      <!--SUV -->
      <img src="../assets/img/BMW_Série_1.jpg" alt="bmw">
      <div class="vehicule-contenu">
        <div class="titre">VOLKSWAGEN Golf VII Golf SW 1.5 eTSI OPF 150 DSG7 R-Line 5p</div>
        <div class="details">Essence · 13 000 km · 2024 · Montauban (82)</div>
        <div class="prix">120 DT/Jour</div>
        <div class="concessionnaire">PR AUTOMOBILES</div>
        <a href="#" class="boutton" title="Voir plus">Réserver</a>
      </div>
    </div>

    <div class="vehicule">
      <!--BERLINE -->
      <img src="../assets/img/location-de-voiture-berline.jpg" alt="skoda">
      <div class="vehicule-contenu">
        <div class="titre">Škoda Octavia de 4e génération (Octavia IV)</div>
        <div class="details">Essence · 83 590 km · 2015 · Mérignac (33)</div>
        <div class="prix">60 DT/Jour</div>
        <div class="concessionnaire">VOLVO - SIPA AUTOMOBILES</div>
        <a href="#" class="boutton" title="Voir plus">Réserver</a>
      </div>
    </div>
    <!--COUPE -->
    <div class="vehicule">
      <img src="../assets/img/image.jpg" alt="maserati">
      <div class="vehicule-contenu">
        <div class="titre">Maserati GranTurismo 2023</div>
        <div class="details">Essence · 7 049 km · 2024 · Montauban (82)</div>
        <div class="prix">1000 DT/Jour</div>
        <div class="concessionnaire">PR AUTOMOBILES</div>
        <a href="#" class="boutton" title="Voir plus">Réserver</a>
      </div>
    </div>
    <div class="vehicule">
      <!--UTILITAIRE -->
      <img src="../assets/img/image2.avif" alt="peugeot">
      <div class="vehicule-contenu">
        <div class="titre">PEUGEOT E-EXPERT</div>
        <div class="details">Essence · 7 049 km · 2024 · Montauban (82)</div>
        <div class="prix">50 DT/Jour</div>
        <div class="concessionnaire">PR AUTOMOBILES</div>
        <a href="#" class="boutton" title="Voir plus">Réserver</a>
      </div>
    </div>
    <div class="vehicule">
      <!--HOLLANDAIS -->
      <img src="../assets/img/velo.webp" alt="birdie">
      <div class="vehicule-contenu">
        <div class="titre">Vélo léger Birdie</div>
        <div class="details">7 049 km · 2024 · Montauban (82)</div>
        <div class="prix">10 DT/Jour</div>
        <div class="concessionnaire">PR AUTOMOBILES</div>
        <a href="#" class="boutton" title="Voir plus">Réserver</a>
      </div>
    </div>
    <div class="vehicule">
      <!--TOUT CHEMIN -->
      <img src="../assets/img/tout-chemin1.avif" alt="HAIBIKE">
      <div class="vehicule-contenu">
        <div class="titre">HAIBIKE</div>
        <div class="details">2024 · Montauban (82)</div>
        <div class="prix">35 DT/Jour</div>
        <div class="concessionnaire">PR AUTOMOBILES</div>
        <a href="#" class="boutton" title="Voir plus">Réserver</a>
      </div>
    </div>
    <div class="vehicule">
      <!--ROUTE -->
      <img src="../assets/img/patria.jpg" alt="patria">
      <div class="vehicule-contenu">
        <div class="titre">Vélo Gravel Tribos 27,5" GRX 2 X 11 vitesses</div>
        <div class="details">2024 · Montauban (82)</div>
        <div class="prix">40 DT/Jour</div>
        <div class="concessionnaire">PR AUTOMOBILES</div>
        <a href="#" class="boutton" title="Voir plus">Réserver</a>
      </div>
    </div>
    <div class="vehicule">
      <!--ELECTRIQUE -->
      <img src="../assets/img/moustache.jpg" alt="moustache">
      <div class="vehicule-contenu">
        <div class="titre">MOUSTACHE 28 DE OCT0BRE 2021</div>
        <div class="details">2024 · Montauban (82)</div>
        <div class="prix">50 DT/Jour</div>
        <div class="concessionnaire">PR AUTOMOBILES</div>
        <a href="#" class="boutton" title="Voir plus">Réserver</a>
      </div>
    </div>
  
</div>

<?php 
require __DIR__ . '/includes/footer.php';
?>