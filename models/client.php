<?php
require_once '../config/database.php';
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


?>