<?php
require_once '../config/database.php';
require_once '../models/reservation.php';


if (!isset($pdo)){
    die("ERREUR: Connexion DB échouée. Vérifiez database.php");
}else{
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo "<pre>POST reçu:</pre>";
        $user_name = isset($_SESSION['user_name']) ? trim($_SESSION['user_name']) : null;
        $user_email = isset($_SESSION['user_email']) ? trim($_SESSION['user_email']) : null;
        $phoneNumber = isset($_SESSION['phoneNumber']) ? trim($_SESSION['phoneNumber']) : null;

        $agency_id = isset($_POST['agency_id']) ? trim($_POST['agency_id']) : null;
        $vehicule_id = isset($_POST['vehicule_id']) ? trim($_POST['vehicule_id']) : null;
        $date_debut = isset($_POST['date_debut']) ? trim($_POST['date_debut']) : null;
        $date_fin = isset($_POST['date_fin']) ? trim($_POST['date_fin']) : null;
        $lieu_prise_en_charge = isset($_POST['lieu_prise']) ? trim($_POST['lieu_prise']) : null;
        $lieu_restitution = isset($_POST['lieu_restitution']) ? trim($_POST['lieu_restitution']) : null;
        $status = isset($_POST['status']) ? trim($_POST['status']) : null;
        
        if (ajouteDemande($pdo, $agency_id, $user_name, $user_email, $phoneNumber, $vehicule_id, $date_debut, $date_fin, $lieu_prise_en_charge, $lieu_restitution, $status)){
            $_SESSION['success_message'] = "Votre reservation a bien ete ajoute.";
            $_SESSION['success_type'] = 'reservation';

            
            echo "<script>console.log('reservation ajoute normalement avec succes');</script>";
            //header("Location: /carRental/views/auth/success.php");
            //exit();
            header("Location: /carRental/views/feedback.php");
            exit();
        }else{
            echo "<script>console.log('reservation non ajoute');</script>";
            $_SESSION['error_message'] = "impossible d'effectue cette reservation";
            $_SESSION['error_type'] = 'reservation';
            header("Location: /carRental/views/feedback.php");
            echo "<script>console.log(" . json_encode($agency_id) . ");</script>";
            //exit();
        }
        
    }else{
        echo "<script>console.log('POST non reçu');</script>";
    }
}

?>