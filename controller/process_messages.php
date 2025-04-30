<?php

require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../models/admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_POST['action'] == "deletMessage"){
            deleteMessage($pdo, $_POST['message_id']);
            header("Location: /carRental/views/dash-board/admin.php");
            exit();
        }elseif($_POST['action'] == "deletClient"){
            deleteClient($pdo, $_POST['client_id']);
            header("Location: /carRental/views/dash-board/admin.php");
            exit();
        }elseif($_POST['action'] == "deletAgency"){
            deleteAgency($pdo, $_POST['agency_id']);
            header("Location: /carRental/views/dash-board/admin.php");
            exit();
        }
    }

    // Initialize variables and error array
    $errors = [];
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    if (empty($subject)) {
        $errors['subject'] = 'Subject is required';
    } elseif (strlen($subject) > 100) {
        $errors['subject'] = 'Subject too long (max 100 characters)';
    }
    
    if (empty($message)) {
        $errors['message'] = 'Message is required';
    } elseif (strlen($message) > 1000) {
        $errors['message'] = 'Message too long (max 1000 characters)';
    }
    // If no errors, proceed with processing
    if (empty($errors)) {
        try {
            
            $stmt = $pdo->prepare("INSERT INTO message (email, object, content) VALUES (?, ?, ?)");
            $stmt->execute([$email, $subject, $message]);
            echo "<script>console.log('heelow world');</script>";
            
            $_SESSION['success'] = true;
            $_SESSION['success_message'] = 'Message sent successfully!';
            header('Location: /carRental/views/feedback.php');
            exit();
        } catch (Exception $e) {
            $errors['database'] = 'Error saving message: ' . $e->getMessage();
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    } else {
        
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

}

function getMessages($pdo) {
    try {
        $statement = $pdo->prepare("SELECT * FROM message ORDER BY created_at DESC");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('Database error: ' . $e->getMessage());
        return [];
    }
}
?>