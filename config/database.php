<?php
$host = 'localhost';
$db   = 'carrental'; // Doit correspondre exactement au nom dans MySQL
$user = 'root';      // Utilisateur par défaut XAMPP
$pass = '';          // Mot de passe par défaut XAMPP (vide)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Test supplémentaire
    $pdo->query("SELECT 1")->fetch();
} catch (PDOException $e) {
    die("ERREUR CONNEXION DB: " . $e->getMessage());
}