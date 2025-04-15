<?php
//session_start();
require __DIR__ . '/../../config/database.php';

/*
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /carRental/index.php');
    exit();
}
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $id = $_POST['id'];

    try {
        switch ($type) {
            case 'user':
            case 'agency':
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                break;
            case 'car':
                $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
                break;
            default:
                throw new Exception("Invalid delete type");
        }
        
        $stmt->execute([$id]);
        header("Location: /carRental/views/dash-board/admin.php");
        exit();

    } catch (PDOException $e) {
        die("Delete failed: " . $e->getMessage());
    }
}
?>