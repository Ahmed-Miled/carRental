<?php
// AuthTest.php sécurisé

// Données de test
$test_email = "test1@gmail.com"; // Email valide (existant en base)
$test_password = "' OR '1'='1";  // Tentative d'injection SQL

// Connexion à la base de données
$conn = mysqli_connect("localhost", "root", "", "carrental");

// Vérifier la connexion
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

// Préparation de la requête sécurisée
$stmt = mysqli_prepare($conn, "SELECT * FROM clients WHERE email = ? AND password = ?");

if ($stmt) {
    // Lier les paramètres
    mysqli_stmt_bind_param($stmt, "ss", $test_email, $test_password);

    // Exécuter la requête
    mysqli_stmt_execute($stmt);

    // Récupérer le résultat
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "🔓 Vulnérabilité toujours présente !";
    } else {
        echo "🔒 Système sécurisé : Injection SQL bloquée.";
    }

    // Fermer le statement
    mysqli_stmt_close($stmt);
} else {
    echo "Erreur dans la préparation de la requête.";
}

// Fermer la connexion
mysqli_close($conn);
?>