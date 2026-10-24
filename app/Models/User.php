<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    // Trouve un utilisateur actif par son email
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Trouve un utilisateur actif par son ID
    public function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Vérifie le mot de passe avec Bcrypt (Sécurisé)
    public function verifyPassword($email, $password)
    {
        $user = $this->findByEmail($email);
        
        // password_verify compare le mot de passe en clair avec le hash Bcrypt
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }

    // Récupère tous les utilisateurs NON supprimés
    public function findAll()
    {
        $sql = "SELECT * FROM users WHERE deleted_at IS NULL ORDER BY last_name, first_name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crée un nouvel utilisateur
    public function insert($data)
    {
        $sql = "INSERT INTO users (last_name, first_name, phone, email, password_hash, is_admin) 
                VALUES (:last_name, :first_name, :phone, :email, :password_hash, :is_admin)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':last_name'     => $data['last_name'],
            ':first_name'    => $data['first_name'],
            ':phone'         => $data['phone'],
            ':email'         => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':is_admin'      => $data['is_admin']
        ]);
    }

    // Met à jour le rôle (admin ou non) d'un utilisateur
    public function updateRole($id, $isAdmin)
    {
        $sql = "UPDATE users SET is_admin = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$isAdmin, $id]);
    }

    // Suppression logique (Soft Delete)
    public function softDelete($id)
    {
        $sql = "UPDATE users SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Met à jour le mot de passe d'un utilisateur
    public function updatePassword($id, $newPasswordHash)
    {
        $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$newPasswordHash, $id]);
    }
}
?>