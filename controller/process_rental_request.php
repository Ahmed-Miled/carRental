<?php
    
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/reservation.php';


if (!isset($pdo)){
    echo "<script>console.log('ERREUR: Connexion DB échouée. Vérifiez database.php');</script>";
    
    die("ERREUR: Connexion DB échouée. Vérifiez database.php");
}else{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_POST['action'] == "approve"){
            ajouteReservation($pdo, $_POST['numDemande']);
            deleteDemande($pdo, $_POST['numDemande']);
            setStatus($pdo, "booked", $_POST['vehicule_id']);
            
            echo "<script>console.log('normalment tawa jsut tfassa5 mel rental request table ');</script>";
            header("Location: /carRental/views/dash-board/agencyDashBoard.php");
            exit();
        }elseif($_POST['action'] == "reject"){
            // fasa5 el karhba mel demande
            
            deleteDemande($pdo, $_POST['numDemande']);
            setStatus($pdo, "available", $_POST['vehicule_id']);
            $_SESSION['success_message'] = "Votre Voiture a bien ete ajoute.";
            $_SESSION['success_type'] = 'Ajoute Voiture';
            header("Location: /carRental/views/dash-board/agencyDashBoard.php");
            exit();

        }
    }else{
        echo "<script>console.log('stanna el post');</script>";
        
    }
}


?>
