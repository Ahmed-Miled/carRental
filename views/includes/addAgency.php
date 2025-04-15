<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /carRental/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (name, phoneNumber, email, password, type)
            VALUES (?, ?, ?, ?, 'agency')
        ");
        $stmt->execute([$name, $phone, $email, $password]);
        
        header("Location: adminDashBoard.php");
        exit();

    } catch (PDOException $e) {
        die("Agency creation failed: " . $e->getMessage());
    }
}