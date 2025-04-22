<?php
    
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/vehicule.php';


if (!isset($pdo)){
    echo "<script>console.log('ERREUR: Connexion DB échouée. Vérifiez database.php');</script>";
    
    die("ERREUR: Connexion DB échouée. Vérifiez database.php");
}else{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $marque = $_POST['marque'];
        $model = $_POST['model'];
        $Kilometrage = $_POST['kilometrage'];
        $annee = $_POST['year'];
        $prix = $_POST['price_per_day'];
        $image = $_POST['image'];
        $nbr_place = $_POST['nbr_place'];
        $nbr_cylindres = $_POST['nbr_cylindres'];
        $boite_vitesse = $_POST['boite_vitesse'];
        $carburant = $_POST['carburant'];
        $status = 'available';
        $id_agence = $_POST['agency_id'];

        ajouteVehicle($pdo, $marque, $model, $Kilometrage, $annee, $prix, $image, $nbr_place, $nbr_cylindres, $boite_vitesse, $carburant, $status, $id_agence);

        

        header("Location: /carRental/views/feedback.php");
        exit();
    }
}
?>