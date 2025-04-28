<?php
require_once '../config/database.php';
require_once '../models/client.php';
session_start(); // Still needed in each file

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    die(json_encode(['status' => 'error', 'message' => 'Not logged in']));
}else{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if($_POST['action'] == "deleteClientAccount"){
            echo "<script>console.log('delete account have been called')</script>";
            if (verifyClientAccount($pdo, $_POST['user_id'],  $_POST['password'] )) {
                deleteClientAccount($pdo, $_SESSION['user_id']);
                header('Location: /carRental/views/auth/logout.php');
                exit();

            }else{
                $_SESSION['error_message'] = "Mot de passe incorrect";
                $_SESSION['error_type'] = 'signup';
                header('Location: /carRental/views/feedback.php');
                exit();
            }
        }elseif ($_POST['action'] == "updateAgency") {
            
            // Get form data
            $username = $_POST['name'] ?? null;
            $phoneNumber = $_POST['phone'] ?? null;
            $address = $_POST['address'] ?? null;
            $email = $_SESSION['user_email']; // Using email from session

            echo "<script>console.log(" . json_encode($username) .");</script>";
            echo "<script>console.log(" . json_encode($phoneNumber) .");</script>";
            echo "<script>console.log(" . json_encode($address) .");</script>";
            echo "<script>console.log(" . json_encode($email) .");</script>";

            updateAgency($pdo, $username, $phoneNumber, $address, $email);


                // Update session data
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