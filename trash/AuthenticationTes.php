<?php 
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client; // Correct spelling: GuzzleHttp

require_once __DIR__.'/../models/client.php';


// Ensure errors are displayed for debugging during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//use PDO; // Import PDO class

// Define BASE_PATH relative to this test file's directory
// Adjust the number of '..' if your 'Test' directory is nested differently
if (!defined('BASE_PATH')) {
    // Assuming Test directory is directly inside the project root (e.g., /carRental/Test)
    define('BASE_PATH', dirname(__DIR__, 1));
    // If Test is inside 'src' or similar, it might be: define('BASE_PATH', dirname(__DIR__, 2));
}

// --- Include Necessary Model Files ---
// Make sure the model functions are available early or within setUp
$modelPath = BASE_PATH . '/models/client.php'; // Adjust path if needed
if (!file_exists($modelPath)) {
     // Can't proceed without the model if tests rely on its functions directly
     die("Error: Required model file not found: " . $modelPath . "\n");
}
require_once $modelPath; // Include the file with createClientAccount() and potentially getClient()

class AuthenticationTest extends TestCase
{
    protected ?PDO $pdo = null; // Use nullable type hint

    /**
     * Set up environment before each test.
     * This includes starting session, getting DB connection, and cleaning up.
     */
    protected function setUp(): void
    {
        // --- Session Handling ---
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_write_close(); // Close any lingering session
        }
        // Start session *before* modifying $_SESSION
        // Suppress headers already sent warning which can occur in CLI
        @session_start();

        // --- Reset Superglobals ---
        $_POST = [];
        $_GET = [];
        $_SESSION = []; // Clear session data *after* starting it

        // --- Database Connection ---
        $configPath = BASE_PATH . '/config/database.php';
        if (!file_exists($configPath)) {
            $this->markTestSkipped('Database config file not found: ' . $configPath);
            return; // Stop setUp if config is missing
        }
        $config = include $configPath;

        // Check if essential keys exist in config
        // Adjust 'db_name' if your config uses 'dbname' or similar
        if (!isset($config['host']) || !isset($config['db_name']) || !isset($config['username']) /*|| !isset($config['password'])*/) {
             $this->markTestSkipped('Database configuration is incomplete (host, db_name, username required).');
             return; // Stop setUp if config is incomplete
        }

        try {
            // Use the correct DSN format and database name key
            $dsn = "mysql:host={$config['host']};dbname={$config['db_name']}";
            $this->pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'] ?? null, // Use null coalescing for optional password
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Recommended for development/testing
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Optional: consistent fetch mode
                    PDO::ATTR_EMULATE_PREPARES   => false, // Recommended for security/performance
                ]
            );

             // --- Clean up Test Database Before Each Test ---
             // IMPORTANT: Ensure this runs against a TEST database.
             $this->pdo->exec('DELETE FROM clients WHERE email LIKE "%@test.example.com"');
             $this->pdo->exec('DELETE FROM agency WHERE email LIKE "%@test.example.com"');
             // Add deletes for other relevant tables if necessary

        } catch (\PDOException $e) {
            $this->markTestSkipped('Database connection or cleanup failed in setUp: ' . $e->getMessage());
        }
    }

    /**
     * Clean up after each test.
     */
    protected function tearDown(): void
    {
        // Destroy the session
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset(); // Remove session data variables
            session_destroy(); // Destroy the session itself
        }

        // Close DB connection
        $this->pdo = null;

        // Reset globals again for good measure
        $_POST = [];
        $_GET = [];
        $_SESSION = [];
    }

    // --- Helper: Include Controller ---
    private function includeController(): void
    {
        $controllerPath = BASE_PATH . '/controller/traitemant.php'; // Adjust if needed
        if (!file_exists($controllerPath)) {
            $this->fail('Controller file not found: ' . $controllerPath);
        }
        // Prevent accidental output from controller messing up tests
        ob_start();
        try {
            include $controllerPath;
        } catch (\Exception $e) {
            // Catch potential errors within the controller itself
            ob_end_clean(); // Clean buffer even if error occurs
            $this->fail("Error occurred during controller execution: " . $e->getMessage());
        }
        ob_end_clean(); // Discard output if controller ran successfully
    }

    // --- Test Methods ---

    /**
     * Test login attempt with a non-existent email.
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testLoginWithNonExistentEmail(): void
    {
        // Arrange: Simulate POST data
        $_POST['action'] = 'login';
        $_POST['role'] = 'client';
        $_POST['logInEmail'] = 'nosuchuser@test.example.com'; // Use test domain
        $_POST['logInPassword'] = 'anypassword';

        // Act: Execute the controller script
        $this->includeController();

        // Assert: Check session variables for error state
        $this->assertIsArray($_SESSION ?? null, '$_SESSION should be an array after controller execution.');
        // Check session only if it's an array (it should be if controller ran and called session_start)
        if(is_array($_SESSION)) {
            $this->assertArrayHasKey('error_message', $_SESSION, 'Session should contain an error message.');
            // Make assertion more specific if possible (match exact error message)
            $this->assertStringContainsStringIgnoringCase('email incorrect', $_SESSION['error_message'], 'Error message should indicate incorrect email.');
            $this->assertArrayHasKey('error_type', $_SESSION, 'Session should contain an error type.');
            $this->assertEquals('authentification', $_SESSION['error_type'], 'Error type should be authentification.');
            $this->assertArrayNotHasKey('logged_in', $_SESSION, 'User should not be marked as logged in.');
        }
    }

    /**
     * Test login attempt with an existing email but wrong password.
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testLoginWithWrongPassword(): void
    {
        // Arrange: Ensure a test user exists
        if (!function_exists('createClientAccount')) {
            $this->markTestSkipped('createClientAccount function not found.');
        }
        try {
            // Use a unique email for this test
            createClientAccount($this->pdo, 'Password Test', 'passtest@test.example.com', 'correct_password', '222', null, 'client');
        } catch (\PDOException | \Exception $e) { // Catch PDO or other errors
             $this->fail('Failed to create test user for wrong password test: ' . $e->getMessage());
        }

        // Arrange: Simulate POST data
        $_POST['action'] = 'login';
        $_POST['role'] = 'client';
        $_POST['logInEmail'] = 'passtest@test.example.com'; // Existing user
        $_POST['logInPassword'] = 'wrong_password';        // Incorrect password

        // Act: Execute the controller script
        $this->includeController();

        // Assert: Check session variables for error state
        $this->assertIsArray($_SESSION ?? null, '$_SESSION should be an array.');
         if(is_array($_SESSION)) {
            $this->assertArrayHasKey('error_message', $_SESSION, 'Session should contain an error message.');
            // Adjust expected message based on your actual controller output
            $this->assertStringContainsStringIgnoringCase('Mots de passe incorrect', $_SESSION['error_message'], 'Error message should indicate incorrect password.');
            $this->assertArrayHasKey('error_type', $_SESSION, 'Session should contain an error type.');
            $this->assertEquals('authentification', $_SESSION['error_type'], 'Error type should be authentification.');
            $this->assertArrayNotHasKey('logged_in', $_SESSION, 'User should not be marked as logged in.');
         }
    }


    /**
     * Test a successful client login.
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSuccessfulClientLogin(): void
    {
        // Arrange: Ensure a test user exists
        if (!function_exists('createClientAccount')) {
            $this->markTestSkipped('createClientAccount function not found.');
        }
         try {
            // Use a unique email for this test
            createClientAccount($this->pdo, 'Login Success Test', 'loginsuccess@test.example.com', 'goodpass', '333', null, 'client');
         } catch (\PDOException | \Exception $e) {
             $this->fail('Failed to create test user for successful login test: ' . $e->getMessage());
         }

        // Arrange: Simulate POST data
        $_POST['action'] = 'login';
        $_POST['role'] = 'client';
        $_POST['logInEmail'] = 'loginsuccess@test.example.com'; // Existing user
        $_POST['logInPassword'] = 'goodpass';                   // Correct password

        // Act: Execute the controller script
        $this->includeController();

        // Assert: Check session variables for successful login state
        $this->assertIsArray($_SESSION ?? null, '$_SESSION should be an array.');
         if(is_array($_SESSION)) {
            $this->assertArrayHasKey('logged_in', $_SESSION, 'Session should have logged_in key.');
            $this->assertTrue($_SESSION['logged_in'], 'User should be marked as logged in.');
            $this->assertArrayHasKey('role', $_SESSION, 'Session should have role key.');
            $this->assertEquals('client', $_SESSION['role'], 'Role should be client.');
            $this->assertArrayHasKey('user_email', $_SESSION, 'Session should have user_email key.');
            $this->assertEquals('loginsuccess@test.example.com', $_SESSION['user_email'], 'Session should contain correct user email.');
            $this->assertArrayHasKey('user_name', $_SESSION, 'Session should have user_name key.');
            $this->assertEquals('Login Success Test', $_SESSION['user_name'], 'Session should contain correct user name.');
            // Optionally check that no error message is present
            $this->assertArrayNotHasKey('error_message', $_SESSION, 'Session should not contain an error message on successful login.');
            $this->assertArrayNotHasKey('error_type', $_SESSION, 'Session should not contain an error type on successful login.');
         }
    }

     // --- Optional: Signup Test Example ---
     /**
      * Test a successful client signup.
      * @runInSeparateProcess
      * @preserveGlobalState disabled
      */
     /* // Uncomment block to enable signup test
     public function testSuccessfulClientSignup(): void
     {
         // Arrange: Simulate POST data for signup
         $signupEmail = 'newsignup@test.example.com'; // Use test domain
         $_POST['action'] = 'signup';
         $_POST['role'] = 'client'; // Assuming role is set during signup POST
         $_POST['SignUpName'] = 'New Signup Client';
         $_POST['SignUpPhoneNumber'] = '444555666';
         $_POST['SignUpEmail'] = $signupEmail;
         $_POST['SignUpPassword'] = 'signuppass';
         // Add any other required fields for signup in your controller (e.g., confirm password)
         // $_POST['ConfirmPassword'] = 'signuppass';

         // Act: Execute the controller script (handles signup action)
         $this->includeController();

         // Assert: Check session state after signup (should be logged in)
         $this->assertIsArray($_SESSION ?? null, '$_SESSION should be an array after signup.');
         if(is_array($_SESSION)) {
             $this->assertArrayHasKey('logged_in', $_SESSION, 'Session should have logged_in key after signup.');
             $this->assertTrue($_SESSION['logged_in'], 'User should be logged in after signup.');
             $this->assertArrayHasKey('role', $_SESSION, 'Session should have role key.');
             $this->assertEquals('client', $_SESSION['role'], 'Role should be client after signup.');
             $this->assertArrayHasKey('user_email', $_SESSION, 'Session should have user_email key.');
             $this->assertEquals($signupEmail, $_SESSION['user_email'], 'Session should contain signup email.');
             $this->assertArrayHasKey('user_name', $_SESSION, 'Session should have user_name key.');
             $this->assertEquals('New Signup Client', $_SESSION['user_name'], 'Session should contain signup name.');
             $this->assertArrayNotHasKey('error_message', $_SESSION, 'Session should not contain error message on successful signup.');
         }

         // Assert: Check database state to confirm user creation
         if (!function_exists('getClient')) { // Assumes getClient function exists in model
             $this->markTestSkipped('getClient function not found, cannot verify database state for signup.');
         } else {
             try {
                 $user = getClient($this->pdo, $signupEmail, 'client'); // Fetch the newly created user
                 $this->assertNotEmpty($user, 'User should exist in the database after signup.');
                 $this->assertIsArray($user, 'getClient should return an array.');
                 // Adjust keys ('fullName', 'phoneNumber', 'password') based on your actual DB schema and getClient return format
                 $this->assertEquals('New Signup Client', $user['fullName'] ?? null, 'Database record should have correct name.');
                 $this->assertEquals('444555666', $user['phoneNumber'] ?? null, 'Database record should have correct phone number.');
                 $this->assertNotEmpty($user['password'] ?? null, 'Password hash should exist and be non-empty in database.');
                 // DO NOT assert the plain text password - assert the hash exists or is valid if you have a verify function
             } catch (\PDOException | \Exception $e) {
                 $this->fail('Failed to query database for signed up user: ' . $e->getMessage());
             }
         }
     }
     */

} // End of AuthenticationTest class