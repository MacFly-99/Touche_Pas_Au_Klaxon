<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ProfileController extends Controller
{
    // Affiche la page de profil
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        $this->render('profile/index', ['user' => $_SESSION['user']]);
    }

    // Traite le formulaire de changement de mot de passe
    public function updatePassword()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
            return;
        }

        $this->verifyCsrfToken();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];
        $userModel = new User();
        
        // On force l'ID en entier pour être sûr
        $userId = (int)$_SESSION['user']['id'];
        $user = $userModel->findById($userId);

        if (!password_verify($currentPassword, $user['password_hash'])) {
            $errors[] = "Votre mot de passe actuel est incorrect.";
        }

        if (strlen($newPassword) < 6) {
            $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
        }

        if ($newPassword === $currentPassword) {
            $errors[] = "Le nouveau mot de passe doit être différent de l'ancien.";
        }

        if (!empty($errors)) {
            $this->render('profile/index', [
                'user' => $_SESSION['user'],
                'errors' => $errors
            ]);
            return;
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($userModel->updatePassword($userId, $newHash)) {
            $_SESSION['flash']['success'] = "Votre mot de passe a été modifié avec succès !";
            $this->redirect('/profile');
        } else {
            $_SESSION['flash']['error'] = "Une erreur est survenue lors de la mise à jour.";
            $this->redirect('/profile');
        }
    }
}
?>