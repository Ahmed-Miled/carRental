<?php

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
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $table = getTable($role);
    if ($address == null ){

        $stmt = $pdo->prepare("INSERT INTO $table (fullName, email, password, phoneNumber) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $hashed_password, $phoneNumber]);
        return true;
    }else{
        $stmt = $pdo->prepare("INSERT INTO agency (fullName, email, password, phoneNumber, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $hashed_password, $phoneNumber, $address]);
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
            //$car['car_id'] = $reservation['car_id'];
            $car['reservation_id'] = $reservation['id'];

            $cars[] = $car;
        }
    }
    
    echo "<script>console.log('get rental history has been called')</script>";
    
    return $cars;
}



function verifyClientAccount($pdo, $user_id, $password) {
    // 1. Récupérer le hachage stocké
    $stmt = $pdo->prepare("SELECT password FROM clients WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) { 
        return true; //  Mot de passe correct
    }
    return false; //  Échec
}

function verifyAgencyAccount($pdo, $user_id, $password) {
    // 1. Récupérer le hachage stocké
    $stmt = $pdo->prepare("SELECT password FROM agency WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) { 
        return true; //  Mot de passe correct
    }
    return false; //  Échec
}

function deleteClientAccount($pdo, $id){

    // Delete the client
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    echo "<script>console.log('delete client has been called')</script>";

    return true;
}
function deleteAgencyAccount($pdo, $id){

    // Delete the client
    $stmt = $pdo->prepare("DELETE FROM agency WHERE id = ?");
    $stmt->execute([$id]);
    echo "<script>console.log('delete client has been called')</script>";

    return true;
}

function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
    return true;
}
?>