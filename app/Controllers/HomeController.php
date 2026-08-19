<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Trip;

class HomeController extends Controller
{
    public function index()
    {
        $tripModel = new Trip();
        $trips = $tripModel->findAllAvailable();
        $this->render('home/index', ['trips' => $trips]);
    }
}

?>