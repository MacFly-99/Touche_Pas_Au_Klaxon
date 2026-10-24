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
        
        $this->pdo = Database::getInstance();
        $this->userModel = new User();

        // Nettoyage
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $this->pdo->exec("DELETE FROM trips");
        $this->pdo->exec("DELETE FROM users");
        $this->pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Fixture : Création d'un utilisateur
        $hash = password_hash($this->testPassword, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (last_name, first_name, phone, email, password_hash, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Martin', 'Alexandre', '0612345678', 'alexandre.martin@email.fr', $hash, 0]);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM users");
        parent::tearDown();
    }

    public function testFindByEmailReturnsUser()
    {
        $user = $this->userModel->findByEmail('alexandre.martin@email.fr');
        $this->assertIsArray($user);
        $this->assertEquals('alexandre.martin@email.fr', $user['email']);
    }

    public function testFindByEmailReturnsFalseForNonExistentEmail()
    {
        $user = $this->userModel->findByEmail('nonexistent@email.com');
        $this->assertFalse($user);
    }

    public function testVerifyPasswordWithCorrectPassword()
    {
        $user = $this->userModel->verifyPassword('alexandre.martin@email.fr', $this->testPassword);
        $this->assertIsArray($user);
        $this->assertEquals('alexandre.martin@email.fr', $user['email']);
    }

    public function testVerifyPasswordWithIncorrectPassword()
    {
        $user = $this->userModel->verifyPassword('alexandre.martin@email.fr', 'wrongpassword');
        $this->assertFalse($user);
    }

    public function testFindAllReturnsArray()
    {
        $users = $this->userModel->findAll();
        $this->assertIsArray($users);
        $this->assertCount(1, $users, 'Il ne devrait y avoir qu\'un seul utilisateur');
    }

    // NOUVEAU TEST : Vérification du Soft Delete
    public function testSoftDeleteUser()
    {
        $user = $this->userModel->findByEmail('alexandre.martin@email.fr');
        $this->assertIsArray($user);

        // On supprime (soft delete)
        $result = $this->userModel->softDelete($user['id']);
        $this->assertTrue($result);

        // L'utilisateur ne doit plus être trouvé par findByEmail (car on filtre sur deleted_at IS NULL)
        $deletedUser = $this->userModel->findByEmail('alexandre.martin@email.fr');
        $this->assertFalse($deletedUser, 'L\'utilisateur supprimé ne doit plus être trouvable');

        // Mais il doit toujours exister dans la BDD (vérification directe SQL)
        $stmt = $this->pdo->prepare("SELECT deleted_at FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertNotNull($dbUser['deleted_at'], 'La colonne deleted_at doit être remplie');
    }
}
?>