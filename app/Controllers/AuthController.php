<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        if (isset($_SESSION['user'])) {
            $this->redirect('/');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $this->verifyCsrfToken();
        
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModel = new User();
            $user = $userModel->verifyPassword($email, $password);

            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user;
                $this->redirect('/');
            } else {
                $this->render('auth/login', ['error' => 'Invalid email or password']);
            }
        } else {
            $this->render('auth/login');
        }
    }

    public function logout()
    {
        // Supprimer le cookie de session du navigateur
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        $this->redirect('/');
    }
}
?>