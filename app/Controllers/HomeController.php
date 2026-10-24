<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Trip;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Si l'utilisateur n'est pas connecté, on affiche le formulaire de connexion
        if (!isset($_SESSION['user'])) {
            $this->render('auth/login');
            return;
        }

        // 2. S'il est connecté, on affiche les trajets
        $tripModel = new Trip();
        $trips = $tripModel->findAllAvailable();
        
        $this->render('home/index', ['trips' => $trips]);
    }
}
?>