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
            
            
            stopRental($pdo, $_POST['car_id'], $_POST['reservation_id']);
            header("Location: /carRental/views/dash-board/clientDashBoard.php");
            exit();
        }
    }
}
?>