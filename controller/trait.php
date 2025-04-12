<?php
require_once '../config/database.php';

function getUsername($pdo, $email){
    try{
        $stmt = $pdo->prepare("SELECT name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['name'];
    }catch (Exception $e){
        "<script> console.log(" . json_encode($e) . ");</script>";
        return false;
    }
}
function getPhoneNumber($pdo, $email){
    try{
        $stmt = $pdo->prepare("SELECT phoneNumber FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['phoneNumber'];
    }catch (Exception $e){
        "<script> console.log(" . json_encode($e) . ");</script>";
        return false;
    }
}


if (!isset($pdo)){
    die("ERREUR: Connexion DB échouée. Vérifiez database.php");
}else{
    // connxion avec la base du donner est verifier
    $role = $_POST['role'];
    $action = $_POST['action'];
/*
    echo "<script>console.log(" . json_encode($role) . ");</script>";
    echo "<script>console.log(" . json_encode($action) . ");</script>";
*/
    //handeling client rediraction if hi exist in the data base
    if ($action == 'login'){
        $email = $_POST['logInEmail'];
        $password = $_POST['logInPassword'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND type = ? AND password = ?");
        $stmt->execute([$email, $role, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user){
            // directing the user to the client dash board and openning a setion
            session_start();
            // getting user info from the data base (user name, ....)
            
            $username = getUsername($pdo, $email);
            $phoneNumber = getPhoneNumber($pdo, $email);
            if (!$username){
                "<script>console.log('Ooops somthing went wrong while retriving username from the database');</script>";
                // redirect to an error page
            }else{
                $_SESSION['logged_in'] = true;
                $_SESSION['user_name'] = $username; // From your DB
                $_SESSION['user_email'] = $email;
                $_SESSION['phoneNumber'] = $phoneNumber;
                $_SESSION['role'] = $role;
                header('Location: /carRental/index.php');
                exit(); 
                //echo "<script>console.log('user found');</script>";
            }
            
        }else{
            echo "<script>console.log('user not found');</script>";
            // direction the user to login page again with an error message
        }
    }else{
        if ($role == 'client'){
            echo "<script>console.log('creating an account for a user');</script>";
            // check if the email is already used by someone else
            $email = $_POST['SignUpEmail'];
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user){
                // direction the user to login page again with an error message
                header('Location: /carRental/views/auth/auth.php');
                echo "<script>console.log('email already used');</script>";
                exit();
            }else{
                // create an account for the useer and opening a session
                $name = $_POST['SignUpName'];
                $phoneNumber = $_POST['SignUpPhoneNumber'];
                $password = $_POST['SignUpPassword'];
                $type = 'client';
                $stmt = $pdo->prepare("INSERT INTO users (name, phoneNumber, email, password, type) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $phoneNumber, $email, $password, $type]);
                session_start();
                $_SESSION['logged_in'] = true;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['phoneNumber'] = $phoneNumber;
                $_SESSION['role'] = $role;
                header('Location: /carRental/index.php');
                exit();
            }
        }else{
            echo "<script>console.log('agency owner can not create an account');</script>";
        }
    }
}


?>