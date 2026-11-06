<?php
namespace App\Controllers;

use App\Core\Controller;

class LegalController extends Controller
{
    // Méthode pour la page Mentions Légales
    public function notices()
    {
        $this->render('legal/notices');
    }

    // Méthode pour la page CGV (ou GTC)
    public function gtc()
    {
        $this->render('legal/gtc');
    }
}
?>