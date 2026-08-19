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
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModelClass = 'App\\Models\\User';
            $userModel = new $userModelClass();
            $user = $userModel->verifyPassword($email, $password);

            if ($user) {
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
        session_destroy();
        $this->redirect('/');
    }
}

?>