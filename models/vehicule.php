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
    $stmt = $pdo->prepare("INSERT INTO cars (agency_id, marque, model, kilometrage, image, price_per_day, status, year, carburant, nbr_place, nbr_cylindres, boite_vitesse) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_agence, $marque, $model, $Kilometrage, $image, $prix, $status, $annee, $carburant, $nbr_place, $nbr_cylindres, $boite_vitesse]);
    echo "<script>console.log('ajoute car insert have been called and inserted')</script>";
}

?>


