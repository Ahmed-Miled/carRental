<?php

//require_once '../config/database.php'; athi te5dem
//require_once __DIR__ . '/../config/database.php';
//require_once ROOT_PATH . '/config/database.php';

// Always load paths first
require_once __DIR__ . '/../config/paths.php';

// Now use the constant
require_once ROOT_DIR . '/config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    // how can i check those using for using only php and later i will care about validation them with js 


    // Initialize variables and error array
    $errors = [];
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // 1. Validate Email
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    // 2. Validate Subject
    if (empty($subject)) {
        $errors['subject'] = 'Subject is required';
    } elseif (strlen($subject) > 100) {
        $errors['subject'] = 'Subject too long (max 100 characters)';
    }

    // 3. Validate Message
    if (empty($message)) {
        $errors['message'] = 'Message is required';
    } elseif (strlen($message) > 1000) {
        $errors['message'] = 'Message too long (max 1000 characters)';
    }
    

    // If no errors, proceed with processing
    if (empty($errors)) {
        
        try {

            // Insert into database using prepared statement
            $stmt = $pdo->prepare("INSERT INTO message (email, object, content) VALUES (?, ?, ?)");
            $stmt->execute([$email, $subject, $message]);
            echo "<script>console.log('heelow world');</script>";
            // Set success message
            $_SESSION['success'] = true;
            header('Location: /carRental/views/contact.php');
            exit();
        } catch (Exception $e) {
            $errors['database'] = 'Error saving message: ' . $e->getMessage();
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
    } else {
        // Store errors in session to display them back on the form

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