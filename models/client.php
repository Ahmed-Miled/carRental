<?php
//require_once '../config/database.php';
function getClient($pdo, $email, $role){
    $table = getTable($role);
    try{
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
    }
}


function getTable($role){
    if($role === 'client'){
        return 'clients';
    }elseif ($role === 'agency') {
        return 'agency';
    }else{
        return false;
    }
}


function emailExists($pdo, $email, $role){
    $table = getTable($role);
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $user['email'];
   
}

function  createClientAccount($pdo, $fullName, $email, $password, $phoneNumber, $address, $role){

    $table = getTable($role);
    if ($address == null ){

        $stmt = $pdo->prepare("INSERT INTO $table (fullName, email, password, phoneNumber) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $password, $phoneNumber]);
        return true;
    }else{
        $stmt = $pdo->prepare("INSERT INTO agency (fullName, email, password, phoneNumber, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $password, $phoneNumber, $address]);
        return true;
    }
}

function getClientId($pdo, $email){
    try{
        $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['id'];
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
    }
}

function updateAgency($pdo, $username, $phoneNumber, $address, $email){
    $stmt = $pdo->prepare("UPDATE agency SET fullName = ?, phoneNumber = ?, address = ? WHERE email = ?");
    $stmt->execute([$username, $phoneNumber, $address, $email]);
        
}

function updateClient($pdo, $username, $phoneNumber, $email){
    $stmt = $pdo->prepare("UPDATE clients SET fullName = ?, phoneNumber = ? WHERE email = ?");
    $stmt->execute([$username, $phoneNumber, $email]);
}

function getRedntalHistory($pdo, $email){
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE client_email = ?");
    $stmt->execute([$email]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //return $reservations;
    $cars = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
    $cars->execute([$reservations['car_id']]);
    $cars = $cars->fetchAll(PDO::FETCH_ASSOC);
    echo "<script>console.log('get rental history have been called')</script>";
    echo "<script>console.log(".json_encode($cars).")</script>";
    return $cars;
}

function getRentalHistory($pdo, $email){
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE client_email = ?");
    $stmt->execute([$email]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $cars = [];
    $carStmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");

    foreach ($reservations as $reservation) {
        $carStmt->execute([$reservation['car_id']]);
        $car = $carStmt->fetch(PDO::FETCH_ASSOC);
        //$cars['start_date'] = $reservation['start_date'];
        if ($car) {
            //  merge car + reservation info
            
            $car['start_date'] = date('Y-m-d', strtotime($reservation['start_date']));

            $cars[] = $car;
        }
    }

    echo "<script>console.log('get rental history has been called')</script>";
    echo "<script>console.log(" . json_encode($cars) . ")</script>";
    
    return $cars;
}


function verifyyClientAccount($pdo, $id,  $password ){
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user['password']) {
        echo "<script>console.log('useer password is correct')</script>";
        return true;
    }else{
        return false;
    }
}


function verifyClientAccount($pdo, $user_id, $password) {
    $stmt = $pdo->prepare("SELECT password FROM clients WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password == $user['password']) {
        return true;
    }
    return false;
}

function deleteClientAccount($pdo, $id){
    echo "<script>console.log('delete client Account funtion have been called')</script>";
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE client_id = ?");
    $stmt->execute([$id]);
    echo "<script>console.log('delete reservation have been called')</script>";
    // Delete the client
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    echo "<script>console.log('delete client have been called')</script>";

    return true;
}

?>