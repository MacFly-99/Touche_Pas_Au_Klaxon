<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Agency;
use App\Models\Trip;

class AdminController extends Controller
{
    /**
     * Vérifie que l'utilisateur est connecté ET administrateur
     * Si ce n'est pas le cas, redirige vers la connexion
     */
    private function checkAdmin()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
            $this->redirect('/auth/login');
            exit;
        }
    }

    // --- Gestion des utilisateurs ---
    public function users()
    {
        $this->checkAdmin();
        $userModel = new User();
        $users = $userModel->findAll();
        $this->render('admin/users/index', ['users' => $users]);
    }

    // --- Gestion des agences ---
    public function agencies()
    {
        $this->checkAdmin();
        $agencyModel = new Agency();
        $agencies = $agencyModel->findAll();
        $this->render('admin/agencies/index', ['agencies' => $agencies]);
    }

    public function agencyCreate()
    {
        $this->checkAdmin();
        $this->render('admin/agencies/create');
    }

    public function agencyStore()
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/agencies');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['flash']['error'] = "Agency name is required.";
            $this->redirect('/admin/agencyCreate');
            return;
        }

        $agencyModel = new Agency();
        if ($agencyModel->insert($name)) {
            $_SESSION['flash']['success'] = "Agency created successfully.";
        } else {
            $_SESSION['flash']['error'] = "Error creating agency. It may already exist.";
        }
        $this->redirect('/admin/agencies');
    }

    public function agencyEdit($id)
    {
        $this->checkAdmin();
        $agencyModel = new Agency();
        $agency = $agencyModel->find($id);
        if (!$agency) {
            $_SESSION['flash']['error'] = "Agency not found.";
            $this->redirect('/admin/agencies');
            return;
        }
        $this->render('admin/agencies/edit', ['agency' => $agency]);
    }

    public function agencyUpdate($id)
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/agencies');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['flash']['error'] = "Agency name is required.";
            $this->redirect('/admin/agencyEdit/' . $id);
            return;
        }

        $agencyModel = new Agency();
        if ($agencyModel->update($id, $name)) {
            $_SESSION['flash']['success'] = "Agency updated successfully.";
        } else {
            $_SESSION['flash']['error'] = "Error updating agency.";
        }
        $this->redirect('/admin/agencies');
    }

    public function agencyDelete($id)
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/agencies');
            return;
        }

        $agencyModel = new Agency();
        if ($agencyModel->delete($id)) {
            $_SESSION['flash']['success'] = "Agency deleted successfully.";
        } else {
            $_SESSION['flash']['error'] = "Error deleting agency.";
        }
        $this->redirect('/admin/agencies');
    }

    // --- Gestion des trajets (admin) ---
    public function trips()
    {
        $this->checkAdmin();
        $tripModel = new Trip();
        $trips = $tripModel->findAllForAdmin();
        $this->render('admin/trips/index', ['trips' => $trips]);
    }

    public function tripDelete($id)
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/trips');
            return;
        }

        $tripModel = new Trip();
        if ($tripModel->delete($id)) {
            $_SESSION['flash']['success'] = "Trip deleted successfully.";
        } else {
            $_SESSION['flash']['error'] = "Error deleting trip.";
        }
        $this->redirect('/admin/trips');
    }
}

?>