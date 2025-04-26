<?php

use PHPUnit\Framework\TestCase;

class PerformanceTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $host = '127.0.0.1';
        $db = 'carrental';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function testSelectReservationsUnder100ms()
    {
        $start = microtime(true);

        $stmt = $this->pdo->query("SELECT * FROM reservations");
        $results = $stmt->fetchAll();

        $duration = microtime(true) - $start;

        $this->assertLessThan(0.1, $duration, "Requête trop lente (>100ms)");
        $this->assertNotEmpty($results, "Pas de résultats");
    }

    public function testStress10QueriesUnder1Second()
    {
        $start = microtime(true);

        for ($i = 0; $i < 500; $i++) {
            $stmt = $this->pdo->query("SELECT * FROM reservations ");
            $stmt->fetchAll();
        }

        $duration = microtime(true) - $start;

        $this->assertLessThan(1.0, $duration, "Trop lent sous stress (10 requêtes)");
        echo "si 500 user fait le mem requentte aux meme tepm— Temps total: " . round($duration, 3) . "s\n";
    }

    public function testRampUpUsers()
    {
        echo "\n";
        for ($users = 1; $users <= 6; $users++) {
            $start = microtime(true);

            for ($i = 0; $i < $users; $i++) {
                $stmt = $this->pdo->query("SELECT * FROM reservations LIMIT 3");
                $stmt->fetchAll();
            }

            $duration = microtime(true) - $start;
            echo "Utilisateurs simultanés: $users — Temps total: " . round($duration, 3) . "s\n";
        }

        $this->assertTrue(true); // Juste des logs ici
    }
}