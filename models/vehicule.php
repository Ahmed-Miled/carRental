<?php
function getVehicules($pdo){
    echo "<script>console.log('get vehicule have been called')</script>";
    $stmt = $pdo->prepare("SELECT * FROM cars");
    $stmt->execute();
    $vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $vehicules;
}

function getVehicule($pdo, $vehicule_id){
    echo "<script>console.log('get vehicule have been called')</script>";
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->execute([$vehicule_id]);
    $vehicule = $stmt->fetch(PDO::FETCH_ASSOC);
    return $vehicule;
}

function getAgencyName($pdo, $id){
    
    $stmt = $pdo->prepare("SELECT * FROM agency WHERE id = ?");
    $stmt->execute([$id]);
    $agency = $stmt->fetch(PDO::FETCH_ASSOC);
    return $agency['fullName'];
}

function getRentalRequests($pdo, $agency_id){
    $stmt = $pdo->prepare("SELECT * FROM demande WHERE agency_id = ?");
    $stmt->execute([$agency_id]);
    $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rentals;
}

function getAgencyVehicles($pdo, $agency_id){
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE agency_id = ? ORDER BY created_at DESC");
    $stmt->execute([$agency_id]);
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $vehicles;
}

function  ajouteVehicle($pdo, $marque, $model, $Kilometrage, $annee, $prix, $image, $nbr_place, $nbr_cylindres, $boite_vitesse, $carburant, $status, $id_agence){
    echo "<script>console.log(' ajoute car is called')</script>";
    echo "<script>console.log(" . json_encode($id_agence) . ")</script>";
    echo "<script>console.log(" . json_encode($marque) . ")</script>";
    echo "<script>console.log(" . json_encode($model) . ")</script>";
    echo "<script>console.log(" . json_encode($Kilometrage) . ")</script>";
    echo "<script>console.log(" . json_encode($image) . ")</script>";
    echo "<script>console.log(" . json_encode($prix) . ")</script>";
    echo "<script>console.log(" . json_encode($status) . ")</script>";
    echo "<script>console.log(" . json_encode($annee) . ")</script>";
    echo "<script>console.log(" . json_encode($carburant) . ")</script>";
    echo "<script>console.log(" . json_encode($nbr_place) . ")</script>";
    echo "<script>console.log(" . json_encode($nbr_cylindres) . ")</script>";
    echo "<script>console.log(" . json_encode($boite_vitesse) . ")</script>";
    $stmt = $pdo->prepare("INSERT INTO cars (agency_id, marque, model, kilometrage, image, price_per_day, status, year, carburant, nbr_place, nbr_cylindres, boite_vitesse) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_agence, $marque, $model, $Kilometrage, $image, $prix, $status, $annee, $carburant, $nbr_place, $nbr_cylindres, $boite_vitesse]);
    echo "<script>console.log('ajoute car insert have been called and inserted')</script>";
    
}



function getCar($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function editVehicle($pdo, $id, $price){
    $stmt = $pdo->prepare("UPDATE cars SET price_per_day = ? WHERE id = ?");
    $stmt->execute([$price, $id]);
}
 


function  deleteVehicle($pdo, $car_id, $agency_id){
    $car = (int)$car_id;
    $agency = (int)$agency_id;
    try{
        $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ? AND agency_id = ?");
        $stmt->execute([$car, $agency]);
        header("Location: /carRental/views/dash-board/agencyDashBoard.php");
        exit();
    }catch(Exception $e){
        echo $e->getMessage();
        
        $_SESSION['error_message'] = "Action non autoriser, voiture en cours de location";
        $_SESSION['error_type'] = 'already Rented';
        header("Location: /carRental/views/feedback.php");
        exit();
    }
    
    
}


function addPromotion($pdo, $agency_id, $vehicle_id, $new_price, $end_date, $current_price){
    echo "<script>console.log(" . json_encode($current_price) . ")</script>";
    echo "<script>console.log(" . json_encode($agency_id) . ")</script>";
    echo "<script>console.log('add promotion have been called')</script>";
    try{
        $stmt = $pdo->prepare("INSERT INTO promotions (agency_id, vehicle_id, promotional_price, original_price, end_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$agency_id, $vehicle_id, $new_price, $current_price, $end_date]);
        return true;
    }catch(Exception $e){
        return false;
    }
}

function getVehiculesEnPromotion($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.*,
                p.promotional_price,
                p.original_price,
                p.end_date,
                ROUND((p.original_price - p.promotional_price) / p.original_price * 100) AS discount_percent
            FROM cars c
            INNER JOIN promotions p ON c.id = p.vehicle_id
            WHERE p.end_date > NOW()
            GROUP BY c.id
            ORDER BY RAND()
            LIMIT 4
        ");
        
        $stmt->execute();
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); 

        return $resultat;

    } catch(PDOException $e) {
        error_log("Erreur getVehiculesEnPromotion: " . $e->getMessage());
        return [];
    }
}
?>


