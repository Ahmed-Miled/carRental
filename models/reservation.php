<?php

function ajouteReservation($pdo, $user_name, $user_email, $phoneNumber, $vehicule_id, $date_debut, $date_fin, $lieu_prise_en_charge, $lieu_restitution, $status){
    echo "<script>console.log('ajoute reservation have been called')</script>";
    if ($status == null){
        echo "<script>console.log('status du voiture est  null');</script>";
    }else{
        if (estPossible($pdo, $vehicule_id, $date_debut, $date_fin)){
            //ekri
            echo "<script>console.log('requette wille execute');</script>";
            /*
            echo "<script>console.log(" . json_encode($vehicule_id) . ");</script>";
            echo "<script>console.log(" . json_encode($user_name) . ");</script>";
            echo "<script>console.log(" . json_encode($user_email) . ");</script>";
            echo "<script>console.log(" . json_encode($phoneNumber) . ");</script>";
            echo "<script>console.log(" . json_encode($date_debut) . ");</script>";
            echo "<script>console.log(" . json_encode($date_fin) . ");</script>";
            echo "<script>console.log(" . json_encode($lieu_prise_en_charge) . ");</script>";
            echo "<script>console.log(" . json_encode($lieu_restitution) . ");</script>";
            echo "<script>console.log(" . json_encode($status) . ");</script>";
            */
            $stmt = $pdo->prepare("INSERT INTO demande (vehicule_id	, start_date, end_date, lieu_prise_en_charge, lieu_rest, clientName, clientEmail, phoneNumber) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$vehicule_id, $date_debut, $date_fin, $lieu_prise_en_charge, $lieu_restitution, $user_name, $user_email, $phoneNumber]);
            echo "<script>console.log('requette have been executed');</script>";
            return true;
        }else{
            // nik 3enek
            echo "<script>console.log('reservation non possible');</script>";
            return false;
        }
    }
}

/*
function estPossibleeeeeeeeeeeeeeeeeeee($pdo, $vehicule_id, $date_debut, $date_fin){
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE car_id = ?");
    $stmt->execute([$vehicule_id]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($reservations)){
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE car_id = ? AND end_date >= ? AND start_date <= ?");    
        $stmt->execute([$vehicule_id, $date_debut, $date_fin]);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($reservations)){
            return false;
        }else{
            return true;
        }
    }
    
}
*/

function estPossible($pdo, $vehicule_id, $date_debut, $date_fin){
    echo "<script>console.log('est possible have been called')</script>";
    $sql = "SELECT COUNT(*)
            FROM reservations
            WHERE car_id = ?          
              AND end_date >= ?       
              AND start_date <= ?     
           ";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $vehicule_id, 
        $date_debut,  
        $date_fin     
    ]);
    // Récupérer le nombre de réservations conflictuelles trouvées
    $conflictCount = (int) $stmt->fetchColumn(); // fetchColumn() retourne la valeur de la première colonne (le COUNT)
    return ($conflictCount == 0);
}

?>