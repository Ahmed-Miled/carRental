<?php
// test_sql_injection.php

// Données de test
$test_email = "' OR '1'='1";
$test_password = "123456";

// Connexion MySQL (méthode non sécurisée)
$conn = mysqli_connect("localhost", "root", "", "carrental");

// Requête directe (DANGEREUX)
$query = "SELECT * FROM clients 
          WHERE email = '$test_email' 
          AND password = '$test_password'";

$result = mysqli_query($conn, $query);

// Vérification
if (mysqli_num_rows($result) > 0) {
    echo "🔓 Vulnérable aux injections SQL !";
} else {
    echo "🔒 Système sécurisé.";
}

mysqli_close($conn);
?>