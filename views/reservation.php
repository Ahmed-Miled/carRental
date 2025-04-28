<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../models/vehicule.php';
session_start();

$agency_id = $_GET['agency_id'] ?? null;
//$agency_id = isset($_GET['agency_id']) ? trim($_GET['agency_id']) : null;
$vehicule_id = $_GET['id'] ?? null; // Récupère l'ID de l'URL (?id=123)

$role = $_SESSION['role'] ?? null;



if($role == null){
    echo "<script>console.log('role null');</script>";
    
    $_SESSION['error_message'] = "Veuillez vous connecter";
    $_SESSION['error_type'] = 'authentification'; 
    
    header('Location: /carRental/views/feedback.php');
    exit();

}else{
    echo "<script>console.log('good to go');</script>";
}

$vehicule = null;
if ($vehicule_id) {
    // VOTRE CODE ICI : Interrogez votre base de données pour trouver le véhicule
    // Exemple de structure de données attendue :
    $vehicule = getVehicule($pdo, $vehicule_id);
    if (!$vehicule) {
        echo "<script>console.log('haja wrong  na9ess');</script>";
        $_SESSION['error_message'] = "vehicule non trouvé.";
        $_SESSION['error_type'] = 'introuvable'; 
        header('Location: /carRental/views/feedback.php');
        exit();
    }
    
}

require __DIR__ . '/includes/header.php';
?>

<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/carRental/assets/css/reservation.css">
</head>

<main class="container reservation-page">

    <h1>Réservation du véhicule</h1>
    <hr>

    <div class="reservation-content">

        <!-- Section : Détails du Véhicule -->
        <section class="vehicle-details">
            <h2>Votre Sélection</h2>
            <div class="vehicle-card">
                <div class="vehicle-image">
                    <img src="/carRental/assets/img/<?= htmlspecialchars($vehicule['image'] ?? 'default_car.png') ?>"
                         alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['model']) ?>"
                         onerror="this.onerror=null; this.src='/carRental/assets/img/default_car.png';">
                </div>
                <div class="vehicle-info">
                    <h3><?= htmlspecialchars($vehicule['marque']) ?> <?= htmlspecialchars($vehicule['model']) ?></h3>
                    <p class="price-highlight">Prix : <?= htmlspecialchars(number_format((float)($vehicule['price_per_day'] ?? 0), 2, ',', ' ')) ?> DT / Jour</p>

                    <ul>
                        <li><i class="fas fa-tachometer-alt"></i>Kilometrage: <?= htmlspecialchars($vehicule['kilometrage'] ?? 'non disponible 0') ?>Km</li>
                        <li><i class="fas fa-gas-pump"></i> Carburant: <?= htmlspecialchars($vehicule['carburant'] ?? 'non disponible') ?></li>
                        <li><i class="fas fa-users"></i> Places: <?= htmlspecialchars($vehicule['nbr_place'] ?? 'non disponible') ?></li>
                        <li><i class="fas fa-gear"></i> Boite de vitesse: <?= htmlspecialchars($vehicule['boite_vitesse'] ?? 'non disponible') ?></li>
                        <li><i class="fas fa-cogs"></i> cylindres: <?= htmlspecialchars($vehicule['nbr_cylindres'] ?? 'non disponible') ?></li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Section : Formulaire de Réservation -->
        <section class="reservation-form-section">
            <h2>Complétez votre réservation</h2>
            <?= $agency_id = $_GET['agency_id'] ?? null; ?>
            <form action="/carRental/controller/traitemant_reservation.php" method="POST" id="reservationForm">

                <!-- Important : Inclure l'ID du véhicule pour le traitement -->
                <input type="hidden" name="vehicule_id" value="<?= htmlspecialchars($vehicule['id']) ?>">
                <input type="hidden" name="agency_id" value="<?= htmlspecialchars($agency_id) ?>">
                <input type="hidden" id="prix_total" name="prix_total" >
                
                <?php
                echo "<script>console.log('donner vehucule');</script>";
                echo "<script>console.log(" . json_encode($vehicule) . ");</script>";
                echo "<script>console.log('fin donner vehucule');</script>";

                ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($vehicule['status']) ?>">
                <fieldset>
                    <legend>Dates de location</legend>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_debut">Date de début <span class="required">*</span></label>
                            <input type="date" id="date_debut" name="date_debut" required
                                   min="<?= date('Y-m-d') // Empêche de sélectionner une date passée ?>">
                            <!-- Ajoutez type="time" si vous gérez les heures -->
                        </div>
                        <div class="form-group">
                            <label for="date_fin">Date de fin <span class="required">*</span></label>
                            <input type="date" id="date_fin" name="date_fin" required
                                   min="<?= date('Y-m-d') ?>">
                            <!-- Ajoutez type="time" si vous gérez les heures -->
                        </div>
                    </div>
                     <!-- Espace pour afficher le coût total (calculé via JS ou après soumission) -->
                     <div id="cost-summary" class="cost-summary-placeholder" style="margin-top: 15px; font-weight: bold;">
                        Coût estimé : (sera calculé)
                     </div>
                </fieldset>

                <fieldset>
                    <legend>Choisissez les lieux</legend>
                     <!-- Si l'utilisateur est connecté, vous pouvez pré-remplir ces champs -->
                     <div class="form-group">
                        <label for="lieu_prise">Lieu de prise en charge <span class="required">*</span></label>
                        <select id="lieu_prise" name="lieu_prise" required>
                            <option value="" disabled selected>-- Choisissez un lieu --</option>
                            <option value="agence_aeroport_tun">Agence Aéroport Tunis-Carthage</option>
                            <option value="agence_aeroport_monastir">Agence Aéroport Monastir Habib-Bourguibae</option>
                            <option value="agence_centre_ville_tunisie">Agence Centre-Ville Tunis</option>
                            <option value="agence_sousse">Agence Sousse</option>
                            <option value="agence_sfax">Agence Sfax</option>
                        </select>
                    </div>

                     <div class="form-group">
                        <label for="lieu_restitution">Lieu de restitution <span class="required">*</span></label>
                        <select id="lieu_restitution" name="lieu_restitution" required>
                            <option value="" disabled selected>-- Choisissez un lieu --</option>
                            <option value="agence_aeroport_tunisie">Agence Aéroport Tunis-Carthage</option>
                            <option value="agence_aeroport_monastir">Agence Aéroport Monastir Habib-Bourguibae</option>
                            <option value="agence_centre_ville_tunisie">Agence Centre-Ville Tunis</option>
                            <option value="agence_sousse">Agence Sousse</option>
                            <option value="agence_sfax">Agence Sfax</option>
                        </select>
                    </div>
                </fieldset>

                <fieldset>
                     <legend>Confirmation</legend>
                     <div class="form-group terms-agreement">
                         <input type="checkbox" id="terms" name="terms" value="accepted" required>
                         <label for="terms">J'ai lu et j'accepte les <a href="/conditions-generales.php" target="_blank">conditions générales de location</a>. <span class="required">*</span></label>
                     </div>
                </fieldset>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success btn-reserve">Confirmer la réservation</button>
                    <a href="liste_vehicules.php" class="btn btn-secondary">Annuler</a> <!-- Lien vers la page précédente -->
                </div>

                 <!-- Zone pour afficher les messages d'erreur ou de succès (via PHP/JS) -->
                 <div id="form-messages" class="form-messages" style="margin-top: 20px;">
                 </div>

            </form>
        </section>

    </div> <!-- .reservation-content -->

</main>

<?php
// --- Inclusion du pied de page ---
require __DIR__ . '/includes/footer.php';
?>


<!-- Optionnel : Script JS (par exemple pour validation avancée, calcul de coût, datepicker) -->
<script>
    // --- Logique JavaScript Optionnelle ---

    document.addEventListener('DOMContentLoaded', function() {
        const dateDebutInput = document.getElementById('date_debut');
        const dateFinInput = document.getElementById('date_fin');
        const costSummaryDiv = document.getElementById('cost-summary');
        const dailyRate = <?= (float)($vehicule['price_per_day'] ?? 0) ?>; // Taux journalier du véhicule
        const hiddenInput = document.getElementById('prix_total');

        // Fonction pour mettre à jour la date minimum de fin
        function updateMinEndDate() {
            if (dateDebutInput.value) {
                dateFinInput.min = dateDebutInput.value;
                // Si la date de fin est antérieure à la nouvelle date de début, la réinitialiser (optionnel)
                if (dateFinInput.value && dateFinInput.value < dateDebutInput.value) {
                     dateFinInput.value = dateDebutInput.value;
                }
            } else {
                dateFinInput.min = "<?= date('Y-m-d') ?>"; // Retour au minimum par défaut
            }
             calculateCost(); // Recalculer le coût quand la date de début change
        }

        // Fonction pour calculer le coût estimé
        function calculateCost() {
            if (dateDebutInput.value && dateFinInput.value && dailyRate > 0) {
                const start = new Date(dateDebutInput.value);
                const end = new Date(dateFinInput.value);

                // S'assurer que les dates sont valides et que fin >= début
                if (!isNaN(start.getTime()) && !isNaN(end.getTime()) && end >= start) {
                     // Calculer la différence en jours (en millisecondes)
                     const diffTime = Math.abs(end - start);
                     // Ajouter 1 jour car la location inclut le jour de début et de fin
                     const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) ;

                    const totalCost = diffDays * dailyRate;

                     // Afficher le coût formaté
                    costSummaryDiv.textContent = `Coût estimé (${diffDays} jour${diffDays > 1 ? 's' : ''}) : ${totalCost.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }).replace('EUR', 'DT')}`; // Adaptez la devise si besoin
                    hiddenInput.value = totalCost;
                    } else {
                     costSummaryDiv.textContent = 'Coût estimé : (dates invalides)';
                }
            } else {
                 costSummaryDiv.textContent = 'Coût estimé : (sélectionnez les dates)';
            }
        }

        // Écouteurs d'événements
        dateDebutInput.addEventListener('change', updateMinEndDate);
        dateFinInput.addEventListener('change', calculateCost);

        // Initialiser la date minimum de fin au chargement
        updateMinEndDate();
         calculateCost(); // Calcul initial si dates pré-remplies
    });

    // Vous pouvez ajouter ici plus de validation JS avant soumission si nécessaire
</script>