<?php
    
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/vehicule.php';

session_start();

if (!isset($pdo)){
    echo "<script>console.log('ERREUR: Connexion DB échouée. Vérifiez database.php');</script>";
    
    die("ERREUR: Connexion DB échouée. Vérifiez database.php");
}else{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $action = $_POST["action"];
        if ($action == "add"){
            
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

        }elseif($action == "delete"){
            $agency_id = $_POST['agency_id'];
            $car_id = $_POST['car_id'];
            deleteVehicle($pdo, $car_id, $agency_id);
        }elseif($action == "edit"){
            $id = $_POST['id'];
            $price = $_POST['price'];
            editVehicle($pdo, $id, $price);
            header("Location: /carRental/views/dash-board/agencyDashBoard.php");
            exit();
        }
    }elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
        $action = $_GET['action'];
        $car_id = (int)$_GET['id'];

        if ($action == "getCar"){
            $car = getCar($pdo, $car_id);
            if ($car) {
                include '../views/editVehicleForm.php'; // only the form HTML
            } else {
                echo "<p>Car not found.</p>";
            }
        }

    }
}
?>