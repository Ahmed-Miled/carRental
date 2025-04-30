<?php
require_once '../config/database.php';
require_once '../models/client.php';
session_start(); 

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    die(json_encode(['status' => 'error', 'message' => 'Not logged in']));
}else{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if($_POST['action'] == "deleteClientAccount"){
            echo "<script>console.log('delete account have been called')</script>";
            $hashedpassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            if (verifyClientAccount($pdo, $_POST['user_id'],  $_POST['password'] )) {
                deleteClientAccount($pdo, $_SESSION['user_id']);
                header('Location: /carRental/views/auth/logout.php');
                exit();

            }else{

                $_SESSION['error_message'] = "mots de passer incorrect. Veuillez essayer une autre fois.";
                $_SESSION['error_type'] = 'authentification';
                header('Location: /carRental/views/feedback.php');
                exit();
            }
        }elseif($_POST['action'] == "deleteAgencyAccount"){
            
            $hashedpassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            if (verifyAgencyAccount($pdo, $_POST['user_id'],  $_POST['password'] )) {
                deleteAgencyAccount($pdo, $_SESSION['user_id']);
                header('Location: /carRental/views/auth/logout.php');
                exit();

            }else{

                $_SESSION['error_message'] = "mots de passer incorrect. Veuillez essayer une autre fois.";
                $_SESSION['error_type'] = 'authentification';
                header('Location: /carRental/views/feedback.php');
                exit();
            }

        }elseif ($_POST['action'] == "updateAgency") {
            
            $username = $_POST['name'] ?? null;
            $phoneNumber = $_POST['phone'] ?? null;
            $address = $_POST['address'] ?? null;
            $email = $_SESSION['user_email']; 

            updateAgency($pdo, $username, $phoneNumber, $address, $email);

            $_SESSION['user_name'] = $username;
            $_SESSION['phoneNumber'] = $phoneNumber;
            $_SESSION['address'] = $address;
            $_SESSION['phoneNumber'] = $phoneNumber;
            header("Location: /carRental/views/dash-board/agencyDashBoard.php");
            exit();
        }elseif ($_POST['action'] == "updateClient") {
            $email = $_SESSION['user_email'];
            $username = $_POST['username'] ?? null;
            $phoneNumber = $_POST['phone'] ?? null;
            updateClient($pdo, $username, $phoneNumber, $email);
            $_SESSION['user_name'] = $username;
            $_SESSION['phoneNumber'] = $phoneNumber;
            header("Location: /carRental/views/dash-board/clientDashBoard.php");
            exit();
        }
    }
}

    

?>