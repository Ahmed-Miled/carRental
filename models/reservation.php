<?php

function ajouteDemande($pdo, $agency_id, $user_name, $user_email, $phoneNumber, $vehicule_id, $date_debut, $date_fin, $lieu_prise_en_charge, $lieu_restitution, $status){
    echo "<script>console.log('ajoute reservation have been called')</script>";
    if ($status == null){
        echo "<script>console.log('status du voiture est  null');</script>";
    }elseif($status == 'pending' || $status == "available"){
        if (estPossible($pdo, $vehicule_id, $date_debut, $date_fin)){
            
            $status = 'pending';
            $stmt = $pdo->prepare("INSERT INTO demande (agency_id, vehicule_id	, start_date, end_date, lieu_prise_en_charge, lieu_rest, clientName, clientEmail, phoneNumber, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            	
            
            $stmt->execute([$agency_id, $vehicule_id, $date_debut, $date_fin, $lieu_prise_en_charge, $lieu_restitution, $user_name, $user_email, $phoneNumber, $status]);
            echo "<script>console.log('requette have been executed');</script>";
            return true;
        }else{
            // nik 3enek
            echo "<script>console.log('reservation non possible');</script>";
            return false;
        }
    }
}


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
    echo "<script>console.log('conflict count: " . $conflictCount . "');</script>";
    return ($conflictCount == 0);
}

function getDemande($pdo, $id){
    echo "<script>console.log('get demanade have been called ');</script>";
    $stmt = $pdo->prepare("SELECT * FROM demande WHERE id = ?");
    $stmt->execute([$id]);
    $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<script>console.log('res mta3ha mregla');</script>";
    return $resultat;
}

function ajouteReservation($pdo, $numDemande){
    // 3andi num demande lazem nzid lel reservation

    echo "<script>console.log('ajoute reservation thave been called ');</script>";
    $demande = getDemande($pdo, $numDemande);
    //  id agency_id vehicule_id start_date end_date lieu_prise_en_charge lieu_rest clientName clientEmail phoneNumber status created_at
    echo "<script>console.log('requette inster reservation tawa bch te5dem ');</script>";

    $status = "booked";
    echo "<script>console.log(" . json_encode($demande['start_date']) . ");</script>";
    echo "<script>console.log(" . json_encode($demande['end_date']) . ");</script>";
    echo "<script>console.log(" . json_encode($demande['vehicule_id']) . ");</script>";
    echo "<script>console.log(" . json_encode($status) . ");</script>";
    echo "<script>console.log(" . json_encode($demande['clientEmail']) . ");</script>";

    $statment = $pdo->prepare("INSERT INTO reservations (start_date, end_date, status, car_id, client_email) VALUES (?, ?, ?, ?, ?)");
    $statment->execute([$demande['start_date'], $demande['end_date'], $status, $demande['vehicule_id'], $demande['clientEmail']]);
    echo "<script>console.log('requette inster reservation 5edmet mregla');</script>" ;
    //el reservation fih :
    //	id	start_date	end_date	status	car_id	client_email	created_at	


}

function deleteDemande($pdo, $id){
    $stmt = $pdo->prepare("DELETE FROM demande WHERE id = ?");
    $stmt->execute([$id]);
}

?>