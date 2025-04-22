<?php
    
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/reservation.php';

session_start();

if (!isset($pdo)){
    echo "<script>console.log('ERREUR: Connexion DB échouée. Vérifiez database.php');</script>";
    
    die("ERREUR: Connexion DB échouée. Vérifiez database.php");
}else{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_POST['action'] == "stop_rental"){
            echo "<script>console.log('rental id ');</script>";
            echo "<script>console.log(" . json_encode($_POST['rental_id']) . ");</script>";
            
            stopRental($pdo, $_POST['rental_id'], $_POST['car_id']);
            
        }
    }
}
?>