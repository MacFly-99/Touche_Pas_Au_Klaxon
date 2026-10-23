<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Config\Database;
use PDO;

class UserTest extends TestCase
{
    private $userModel;
    private $pdo;
    private $testPassword = 'W5gc97E6IbUktTVGAmardLpxy';

    protected function setUp(): void
    {
        parent::setUp();
        
        // 1. On récupère la connexion PDO et le modèle
        $this->pdo = Database::getInstance();
        $this->userModel = new User();

        // 2. On nettoie la base (désactivation temporaire des clés étrangères)
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $this->pdo->exec("DELETE FROM trips");
        $this->pdo->exec("DELETE FROM users");
        $this->pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // 3. On crée l'utilisateur avec le VRAI hash (généré automatiquement)
        $hash = password_hash($this->testPassword, PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("INSERT INTO users (last_name, first_name, phone, email, password_hash, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Martin', 'Alexandre', '0612345678', 'alexandre.martin@email.fr', $hash, 0]);
    }

    protected function tearDown(): void
    {
        // Optionnel : On nettoie après le test pour laisser la base propre
        $this->pdo->exec("DELETE FROM users");
        parent::tearDown();
    }

    public function testFindByEmailReturnsUser()
    {
        $user = $this->userModel->findByEmail('alexandre.martin@email.fr');
        $this->assertIsArray($user, 'findByEmail should return an array');
        $this->assertEquals('alexandre.martin@email.fr', $user['email']);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('last_name', $user);
        $this->assertArrayHasKey('first_name', $user);
        $this->assertArrayHasKey('phone', $user);
        $this->assertArrayHasKey('password_hash', $user);
        $this->assertArrayHasKey('is_admin', $user);
    }

    public function testFindByEmailReturnsFalseForNonExistentEmail()
    {
        $user = $this->userModel->findByEmail('nonexistent@email.com');
        $this->assertFalse($user, 'findByEmail should return false for non-existent email');
    }

    public function testVerifyPasswordWithCorrectPassword()
    {
        // On utilise la variable $this->testPassword
        $user = $this->userModel->verifyPassword('alexandre.martin@email.fr', $this->testPassword);
        $this->assertIsArray($user, 'verifyPassword should return user array for correct password');
        $this->assertEquals('alexandre.martin@email.fr', $user['email']);
    }

    public function testVerifyPasswordWithIncorrectPassword()
    {
        $user = $this->userModel->verifyPassword('alexandre.martin@email.fr', 'wrongpassword');
        $this->assertFalse($user, 'verifyPassword should return false for incorrect password');
    }

    public function testVerifyPasswordWithNonExistentEmail()
    {
        $user = $this->userModel->verifyPassword('nonexistent@email.com', 'secret');
        $this->assertFalse($user, 'verifyPassword should return false for non-existent email');
    }

    public function testFindAllReturnsArray()
    {
        $users = $this->userModel->findAll();
        $this->assertIsArray($users, 'findAll should return an array');
        $this->assertNotEmpty($users, 'findAll should return at least one user');

        // Vérifier que chaque élément a les bonnes clés
        $firstUser = $users[0];
        $this->assertArrayHasKey('id', $firstUser);
        $this->assertArrayHasKey('last_name', $firstUser);
        $this->assertArrayHasKey('first_name', $firstUser);
        $this->assertArrayHasKey('email', $firstUser);
        $this->assertArrayHasKey('phone', $firstUser);
        $this->assertArrayHasKey('is_admin', $firstUser);
    }
}
?>