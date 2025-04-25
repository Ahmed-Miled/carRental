<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../models/client.php';
class AuthenticationTest extends TestCase
{
    

private $conn;

// Setup database connection before tests 
    protected function setUp(): void {
        $host = 'localhost';
        $dbname = 'carrental';
        $user = 'root';
        $pass = '';
    
        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->markTestSkipped('Database connection failed: ' . $e->getMessage());
        }
    }

    public function testClientLoginWithValidCredentials() {
        $testEmail = 'ml967799@gmail.com';
        $testPassword = '123';

        // Use your application's function to get the user
        $user = getClient($this->conn, $testEmail, 'client');

        $this->assertIsArray($user, "User with email $testEmail should exist");

        // Verify the password (ensure your application uses password_verify)

        $this->assertTrue(password_verify($testPassword, $user['password']), 
        "Password should match the stored hash");
    }

    public function testClientLoginWithInvalidCredentials() {
        $testEmail = 'emailNotInTable@gmail.com';
        
        $user = getClient($this->conn, $testEmail, 'client');
    
        // Check user doesn't exist
        $this->assertFalse($user, 
            "User with email $testEmail should not exist");
    }
    

public function testLogoutDestroysSession() {
    // Initialiser la session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Simuler un utilisateur connecté
    $_SESSION['user_id'] = 123;
    $_SESSION['email'] = 'test@example.com';

    // Appeler la fonction de déconnexion
    $result = logout();

    // Vérifications
    $this->assertTrue($result);
    $this->assertArrayNotHasKey('user_id', $_SESSION);
    $this->assertArrayNotHasKey('email', $_SESSION);
    $this->assertEquals(PHP_SESSION_NONE, session_status());
}
    protected function tearDown(): void{
        $this->conn = null;
    }

}