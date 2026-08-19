<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    // Trouve un utilisateur par son email
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Vérifie le mot de passe (hash SHA256)
    public function verifyPassword($email, $password)
    {
        $user = $this->findByEmail($email);
        if ($user && hash('sha256', $password) === $user['password_hash']) {
            return $user;
        }
        return false;
    }
}

?>