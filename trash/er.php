<?php

use PHPUnit\Framework\TestCase;
//use GuzzeleHttp\Client;
use GuzzleHttp\Client; // Correct spelling: GuzzleHttp

require_once __DIR__.'/../models/client.php';


class AuthenticationTest extends TestCase
{
    protected $pdo;

    /**
     * Set up environment before each test.
     * This includes starting session and getting DB connection.
     */


     protected function setUp(): void
     {
         // Configuration HTTP
         $this->client = new Client([
             'base_uri' => 'http://localhost/carRental/',
             'timeout'  => 30,
             'verify'   => false
         ]);
 
         // Configuration BDD
         $config = include _DIR_.'/../../config/database.php';
         $this->pdo = new PDO(
             "mysql:host={$config['host']};dbname=carrental",
             $config['username'],
             $config['password']
         );
     }


/*

     protected function setUp(): void
    {
        // Ensure session is started cleanly for each test, especially when running in separate processes.
        // Using @ to suppress warnings if headers already sent in some CLI environments.
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_write_close(); // Close any lingering session
        }
        @session_start();

        // Reset superglobals to simulate a clean request environment
        $_POST = [];
        $_GET = [];
        $_SESSION = []; // Clear session data before each test

        // Include database configuration.
        // IMPORTANT: Ideally, connect to a separate TEST database here.
        // This example uses your existing config, which might modify your dev data.
        //require BASE_PATH . '/config/database.php'; // Use BASE_PATH
        if (!isset($pdo)) {
            $this->markTestSkipped('Database connection could not be established in setUp.');
        }
        $this->pdo = $pdo;

        // Make sure the model functions are available
        //require_once BASE_PATH . '/models/client.php';

        // --- Optional: Clean up/Seed Test Database ---
        // If using a test database, you might want to clear tables
        // and potentially add known test users here. Example:
        // $this->pdo->exec('DELETE FROM clients WHERE email LIKE "%@test.example.com"');
        // $this->pdo->exec('DELETE FROM agency WHERE email LIKE "%@test.example.com"');
        // createClientAccount($this->pdo, 'Test Client', 'client@test.example.com', 'password', '111', null, 'client');
        // ---------------------------------------------
    }
*/

    /**
     * Clean up after each test.
     */
    protected function tearDown(): void
    {
        // Destroy the session
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
         $this->pdo = null; // Close connection if necessary (PDO usually handles this)
    }

    /**
     * Test login attempt with a non-existent email.
     * We expect an error message in the session and redirection (implicitly tested by session state).
     *
     * @runInSeparateProcess  // Crucial: Isolates session and header() calls for this test
     * @preserveGlobalState disabled // Prevents conflicts with global state between processes
     */
    public function testLoginWithNonExistentEmail()
    {
        // 1. Simulate POST data
        $_POST['action'] = 'login';
        $_POST['role'] = 'client';
        $_POST['logInEmail'] = 'nosuchuser@example.com';
        $_POST['logInPassword'] = 'anypassword';

        // 2. Execute the controller script
        // Output buffering captures any echo statements (like your console.log)
        ob_start();
        //include BASE_PATH . '/controller/traitemant.php';
        ob_end_clean(); // Discard the output

        // 3. Assertions: Check session variables for error state
        // Note: We can't directly test the header() redirect easily without mocks,
        // but checking the session state set *before* the redirect is a good indicator.
         $this->assertArrayHasKey('error_message', $_SESSION, 'Session should contain an error message.');
         $this->assertStringContainsStringIgnoringCase('email incorrect', $_SESSION['error_message'], 'Error message should indicate incorrect email.');
         $this->assertEquals('authentification', $_SESSION['error_type'], 'Error type should be authentification.');
         //$this->assertArrayNotHasKey('logged_in', $_SESSION, 'User should not be marked as logged in.'); // Ensure user is not logged in
    }

    /**
     * Test login attempt with an existing email but wrong password.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testLoginWithWrongPassword()
    {
        // Ensure a test user exists (replace with actual test data setup)
        // You might want to create this user in setUp or use a known user from seeding
        createClientAccount($this->pdo, 'Password Test', 'passtest@test.example.com', 'correct_password', '222', null, 'client');

        // 1. Simulate POST data
        $_POST['action'] = 'login';
        $_POST['role'] = 'client';
        $_POST['logInEmail'] = 'passtest@test.example.com'; // Existing user
        $_POST['logInPassword'] = 'wrong_password';        // Incorrect password

        // 2. Execute the controller script
        ob_start();
        //include BASE_PATH . '/controller/traitemant.php';
        ob_end_clean();

        // 3. Assertions: Check session variables for error state
         $this->assertArrayHasKey('error_message', $_SESSION, 'Session should contain an error message.');
         $this->assertStringContainsStringIgnoringCase('Mots de passe', $_SESSION['error_message'], 'Error message should indicate incorrect password.');
         $this->assertEquals('authentification', $_SESSION['error_type'], 'Error type should be authentification.');
         //$this->assertArrayNotHasKey('logged_in', $_SESSION, 'User should not be marked as logged in.');
    }


    /**
     * Test a successful client login.
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSuccessfulClientLogin()
    {
        // Ensure a test user exists
         createClientAccount($this->pdo, 'Login Success Test', 'loginsuccess@test.example.com', 'goodpass', '333', null, 'client');

        // 1. Simulate POST data
        $_POST['action'] = 'login';
        $_POST['role'] = 'client';
        $_POST['logInEmail'] = 'loginsuccess@test.example.com'; // Existing user
        $_POST['logInPassword'] = 'goodpass';                   // Correct password

        // 2. Execute the controller script
        ob_start();
        // NOTE: traitemant.php calls exit() after header(). In @runInSeparateProcess,
        // the script will terminate, but PHPUnit can still capture the state *before* exit.
        //include BASE_PATH . '/controller/traitemant.php';
        ob_end_clean();

        // 3. Assertions: Check session variables for successful login state
        $this->assertTrue(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true, 'User should be marked as logged in.');
        $this->assertEquals('client', $_SESSION['role'], 'Role should be client.');
        $this->assertEquals('loginsuccess@test.example.com', $_SESSION['user_email'], 'Session should contain correct user email.');
        $this->assertEquals('Login Success Test', $_SESSION['user_name'], 'Session should contain correct user name.');
        //$this->assertArrayNotHasKey('error_message', $_SESSION, 'Session should not contain an error message on successful login.');

    }

     // Optional: Add similar tests for 'agency' role and 'signup' action if needed.
     // Example for signup success:
     /**
      * @runInSeparateProcess
      * @preserveGlobalState disabled
      */
     /*
     public function testSuccessfulClientSignup()
     {
         // 1. Simulate POST data
         $_POST['action'] = 'signup';
         $_POST['role'] = 'client';
         $_POST['SignUpName'] = 'New Signup';
         $_POST['SignUpPhoneNumber'] = '444555666';
         $_POST['SignUpEmail'] = 'newsignup@test.example.com'; // Make sure this email doesn't exist
         $_POST['SignUpPassword'] = 'signuppass';

         // 2. Execute
         ob_start();
         include BASE_PATH . '/controller/traitemant.php';
         ob_end_clean();

         // 3. Assertions
         $this->assertTrue(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true);
         $this->assertEquals('client', $_SESSION['role']);
         $this->assertEquals('newsignup@test.example.com', $_SESSION['user_email']);
         $this->assertEquals('New Signup', $_SESSION['user_name']);

         // You could also query the database here to confirm the user was actually inserted
         $user = getClient($this->pdo, 'newsignup@test.example.com', 'client');
         $this->assertNotEmpty($user);
         $this->assertEquals('New Signup', $user['fullName']);
     }
     */

}
