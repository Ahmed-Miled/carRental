<?php
require __DIR__ . '/config/database.php';

try {
    $stmt = $pdo->query("SELECT * FROM users LIMIT 1");
    echo "<h1>Connexion réussie!</h1>";
    echo "<pre>Utilisateurs: ";
    print_r($stmt->fetchAll());
    echo "</pre>";
} catch (PDOException $e) {
    die("<h1>Échec connexion:</h1><pre>" . $e->getMessage() . "</pre>");
}