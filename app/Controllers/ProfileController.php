<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ProfileController extends Controller
{
    // Affiche la page de profil
    public function index()
    {
        // Sécurité : Il faut être connecté
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        $this->render('profile/index', ['user' => $_SESSION['user']]);
    }

    // Traite le formulaire de changement de mot de passe
    public function updatePassword()
    {
        // 1. Vérifications de base
        if (!isset($_SESSION['user'])) {
            $this->redirect('/auth/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
            return;
        }

        $this->verifyCsrfToken();

        // 2. Récupération des données
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];
        $userModel = new User();
        
        // On récupère l'utilisateur en base pour avoir son hash actuel
        $user = $userModel->findById($_SESSION['user']['id']);

        // 3. Validation
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

        // 4. S'il y a des erreurs, on réaffiche la page
        if (!empty($errors)) {
            $this->render('profile/index', [
                'user' => $_SESSION['user'],
                'errors' => $errors
            ]);
            return;
        }

        // 5. Tout est bon : on hache et on met à jour
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($userModel->updatePassword($user['id'], $newHash)) {
            $_SESSION['flash']['success'] = "Votre mot de passe a été modifié avec succès !";
            $this->redirect('/profile');
        } else {
            $_SESSION['flash']['error'] = "Une erreur est survenue lors de la mise à jour.";
            $this->redirect('/profile');
        }
    }
}
?>