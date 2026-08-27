<?php
namespace Tests\Models;

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    private $userModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new User();
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
        $user = $this->userModel->verifyPassword('alexandre.martin@email.fr', 'secret');
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