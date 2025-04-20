<?php
// --- Simulation : Récupérer l'ID du véhicule depuis l'URL ---
// Dans une vraie application, validez et nettoyez cet ID !
$vehicule_id = $_GET['id'] ?? null; // Récupère l'ID de l'URL (?id=123)
session_start();
$role = $_SESSION['role'] ?? null;


if($role == null){
    echo "<script>console.log('role null');</script>";
    
    $_SESSION['error_message'] = "Veuillez vous connecter";
    $_SESSION['error_type'] = 'authentification'; 
    
    header('Location: /carRental/views/error.php');
    exit();

}else{
    echo "<script>console.log('good to go');</script>";
}

// --- Simulation : Récupérer les détails du véhicule depuis la base de données ---
// Vous remplacerez ceci par votre propre logique de base de données
// pour obtenir les infos du véhicule basé sur $vehicule_id.
$vehicule = null;
if ($vehicule_id) {
    // VOTRE CODE ICI : Interrogez votre base de données pour trouver le véhicule
    // Exemple de structure de données attendue :
    $vehicule = [
        'id' => $vehicule_id,
        'marque' => 'VOLKSWAGEN', // Exemple
        'model' => 'Golf VII SW 1.5 eTSI', // Exemple
        'image' => 'offer1.png', // Nom du fichier image (exemple)
        'prixParJour' => 120, // Exemple
        'description_courte' => 'Essence, Automatique, 5 portes', // Exemple
        // Ajoutez d'autres champs pertinents : transmission, carburant, places, etc.
    ];
}

// Si le véhicule n'est pas trouvé, redirigez ou affichez une erreur
if (!$vehicule) {
    // Vous pouvez rediriger vers une page d'erreur ou la liste des véhicules
    // header('Location: liste_vehicules.php?error=notfound');
    // exit;
    // Ou afficher un message directement (moins propre)
    die("Erreur : Véhicule non trouvé.");
}

// --- Inclusion de l'en-tête ---
require __DIR__ . '/includes/header.php';
?>

<head>
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
                    <p><?= htmlspecialchars($vehicule['description_courte'] ?? 'Description non disponible') ?></p>
                    <p class="price-highlight">Prix : <?= htmlspecialchars(number_format((float)($vehicule['prixParJour'] ?? 0), 2, ',', ' ')) ?> DT / Jour</p>
                    <!-- Ajoutez d'autres détails si nécessaire -->
                    <ul>
                        <!-- Exemple: <li><i class="fas fa-gas-pump"></i> Carburant: Essence</li> -->
                        <!-- Exemple: <li><i class="fas fa-users"></i> Places: 5</li> -->
                    </ul>
                </div>
            </div>
        </section>

        <!-- Section : Formulaire de Réservation -->
        <section class="reservation-form-section">
            <h2>Complétez votre réservation</h2>

            <!-- Vous ajouterez votre logique de traitement ici (action="traitement_reservation.php") -->
            <form action="votre_script_de_traitement.php" method="POST" id="reservationForm">

                <!-- Important : Inclure l'ID du véhicule pour le traitement -->
                <input type="hidden" name="vehicule_id" value="<?= htmlspecialchars($vehicule['id']) ?>">

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
                    <legend>Informations personnelles</legend>
                     <!-- Si l'utilisateur est connecté, vous pouvez pré-remplir ces champs -->
                    <div class="form-group">
                        <label for="nom_complet">Nom complet <span class="required">*</span></label>
                        <input type="text" id="nom_complet" name="nom_complet" placeholder="Votre nom et prénom" required
                               value="<?= '' // Pré-remplir si connecté ?>">
                    </div>
                     <div class="form-row">
                        <div class="form-group">
                            <label for="email">Adresse Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="exemple@domaine.com" required
                                value="<?= '' // Pré-remplir si connecté ?>">
                        </div>
                        <div class="form-group">
                            <label for="telephone">Numéro de téléphone <span class="required">*</span></label>
                            <input type="tel" id="telephone" name="telephone" placeholder="Ex: 22 123 456" required
                                pattern="[0-9]{8,}" title="Numéro de téléphone (8 chiffres minimum)"
                                value="<?= '' // Pré-remplir si connecté ?>">
                        </div>
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
                    <button type="submit" class="btn btn-primary btn-reserve">Confirmer la réservation</button>
                    <a href="liste_vehicules.php" class="btn btn-secondary">Annuler</a> <!-- Lien vers la page précédente -->
                </div>

                 <!-- Zone pour afficher les messages d'erreur ou de succès (via PHP/JS) -->
                 <div id="form-messages" class="form-messages" style="margin-top: 20px;">
                    <!-- Les messages apparaîtront ici -->
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
        const dailyRate = <?= (float)($vehicule['prixParJour'] ?? 0) ?>; // Taux journalier du véhicule

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
                     const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                    const totalCost = diffDays * dailyRate;

                     // Afficher le coût formaté
                     costSummaryDiv.textContent = `Coût estimé (${diffDays} jour${diffDays > 1 ? 's' : ''}) : ${totalCost.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' }).replace('EUR', 'DT')}`; // Adaptez la devise si besoin
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