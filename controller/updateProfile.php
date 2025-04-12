<?php
/*
require_once '../config/database.php';

// get user info from webpage
// check if they exsiste
// if no shows an error
// if yes update the info from the back end and refresh the clientDashBoard page

$email = $_SESSION['email'];
$username = $_POST['username'];
$phoneNumber = $_POST['phone'];

echo "<script> console.log('user info: $email, $username, $phoneNumber'); </script>";

$statment = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$statment->execute([$email]);
$user = $statment->fetch(PDO::FETCH_ASSOC);

if(!$user){
    echo "<script> console.log('somthing went wrong while updating user info !!'); </script>";
}else{
    $statment = $pdo->prepare("UPDATE users SET phoneNumber = ?, name = ? WHERE email = ? ");
    $statment->execute([$phoneNumber, $username, $email]);
    header("Location: /carRental/views/dash-board/clientDashBoard.php");
}
*/
?>
<?php
require_once '../config/database.php';
session_start(); // Still needed in each file

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    die(json_encode(['status' => 'error', 'message' => 'Not logged in']));
}

// Get form data
$username = $_POST['username'] ?? null;
$phoneNumber = $_POST['phone'] ?? null;
$email = $_SESSION['user_email']; // Using email from session

// Simple validation
if (empty($username) || empty($phoneNumber) ) {
    die(json_encode(['status' => 'error', 'message' => 'All fields are required']));
}

try {
    // Update user info
    $stmt = $pdo->prepare("UPDATE users SET name = ?, phoneNumber = ? WHERE email = ?");
    $stmt->execute([$username, $phoneNumber, $email]);
    
    // Update session data
    $_SESSION['user_name'] = $username;
    $_SESSION['phoneNumber'] = $phoneNumber;
    
    header("Location: /carRental/views/dash-board/clientDashBoard.php");
    exit();
    
} catch (PDOException $e) {
    // Log the actual error for debugging
    error_log("Update error: " . $e->getMessage());
    
    // Return user-friendly error
    die(json_encode(['status' => 'error', 'message' => 'Update failed']));
}
?>