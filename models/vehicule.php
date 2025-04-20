<?php
function getVehicule($pdo){
    echo "<script>console.log('get vehicule have been called')</script>";
    $stmt = $pdo->prepare("SELECT * FROM cars");
    $stmt->execute();
    $vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $vehicules;
}
function getAgencyName($pdo, $id){
    $stmt = $pdo->prepare("SELECT * FROM agency WHERE id = ?");
    $stmt->execute([$id]);
    $agency = $stmt->fetch(PDO::FETCH_ASSOC);
    return $agency['fullName'];
}
?>