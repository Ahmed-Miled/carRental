<?php
require_once '../config/database.php';
require_once '../models/reservation.php';


if (!isset($pdo)){
    die("ERREUR: Connexion DB échouée. Vérifiez database.php");
}else{
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $vehicule_id = isset($_POST['vehicule_id']) ? trim($_POST['vehicule_id']) : null;
        $date_debut = isset($_POST['date_debut']) ? trim($_POST['date_debut']) : null;
        $date_fin = isset($_POST['date_fin']) ? trim($_POST['date_fin']) : null;
        $lieu_prise_en_charge = isset($_POST['lieu_prise']) ? trim($_POST['lieu_prise']) : null;
        $lieu_restitution = isset($_POST['lieu_restitution']) ? trim($_POST['lieu_restitution']) : null;
        $status = isset($_POST['status']) ? trim($_POST['status']) : null;
        
        if (gerreReservation($pdo, $vehicule_id, $date_debut, $date_fin, $lieu_prise_en_charge, $lieu_restitution, $status)){
            header("Location: /carRental/views/auth/success.php");
            exit();
        }
    }
}

?>