<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    // Affiche le formulaire de connexion ou traite le formulaire
    public function login()
    {
        // Si déjà connecté, on redirige vers l'accueil
        if (isset($_SESSION['user'])) {
            $this->redirect('/');
            return;
        }

        // Si c'est une soumission de formulaire (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
        $this->verifyCsrfToken();
        
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModel = new User();
            $user = $userModel->verifyPassword($email, $password);

            if ($user) {
                
                session_regenerate_id(true);

                // Connexion réussie
                $_SESSION['user'] = $user;
                $this->redirect('/');
            } else {
                // Échec : on affiche le formulaire avec une erreur
                $this->render('auth/login', ['error' => 'Invalid email or password']);
            }
        } else {
            // Affichage du formulaire (GET)
            $this->render('auth/login');
        }
    }

    // Déconnexion
    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }
}

?>