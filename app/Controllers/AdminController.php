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

    public function userCreate()
    {
        $this->checkAdmin();
        $this->render('admin/users/create');
    }

    public function userStore()
    {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
            return;
        }

        $this->verifyCsrfToken();

        $lastName = trim($_POST['last_name'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $isAdmin = (int)($_POST['is_admin'] ?? 0);

        $errors = [];
        
        if (empty($lastName) || empty($firstName) || empty($email) || empty($phone) || empty($password)) {
            $errors[] = "Tous les champs sont obligatoires.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        }

        if (strlen($password) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
        }

        $userModel = new User();
        
        if ($userModel->findByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé par un autre compte.";
        }

        if (!empty($errors)) {
            $this->render('admin/users/create', [
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $email,
            'phone' => $phone,
            'password_hash' => $passwordHash,
            'is_admin' => $isAdmin
        ];

        if ($userModel->insert($data)) {
            $_SESSION['flash']['success'] = "L'utilisateur a été créé avec succès.";
        } else {
            $_SESSION['flash']['error'] = "Une erreur est survenue lors de la création de l'utilisateur.";
        }

        $this->redirect('/admin/users');
    }

    public function userToggleRole($id)
    {
        $id = (int)$id; // Sécurité ID
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
            return;
        }

        $this->verifyCsrfToken();

        if ($id == $_SESSION['user']['id']) {
            $_SESSION['flash']['error'] = "Vous ne pouvez pas changer votre propre rôle.";
            $this->redirect('/admin/users');
            return;
        }

        $userModel = new User();
        $user = $userModel->findById($id);

        if (!$user) {
            $_SESSION['flash']['error'] = "Utilisateur introuvable.";
            $this->redirect('/admin/users');
            return;
        }

        $newRole = $user['is_admin'] == 1 ? 0 : 1;

        if ($userModel->updateRole($id, $newRole)) {
            $roleName = $newRole == 1 ? "Administrateur" : "Utilisateur standard";
            $_SESSION['flash']['success'] = "Le rôle de " . htmlspecialchars($user['first_name']) . " a été changé en : $roleName.";
        } else {
            $_SESSION['flash']['error'] = "Erreur lors de la modification du rôle.";
        }

        $this->redirect('/admin/users');
    }

    public function userDelete($id)
    {
        $id = (int)$id; // Sécurité ID
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
            return;
        }

        $this->verifyCsrfToken(); 

        if (isset($_SESSION['user']['id']) && $id == $_SESSION['user']['id']) {
            $_SESSION['flash']['error'] = "Vous ne pouvez pas supprimer votre propre compte.";
            $this->redirect('/admin/users');
            return;
        }

        $userModel = new User();
        if ($userModel->softDelete($id)) {
            $_SESSION['flash']['success'] = "Utilisateur supprimé avec succès.";
        } else {
            $_SESSION['flash']['error'] = "Erreur lors de la suppression de l'utilisateur.";
        }
        
        $this->redirect('/admin/users');
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

        $this->verifyCsrfToken(); // AJOUT SÉCURITÉ

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['flash']['error'] = "Le nom de l'agence est requis.";
            $this->redirect('/admin/agencyCreate');
            return;
        }

        $agencyModel = new Agency();
        if ($agencyModel->insert($name)) {
            $_SESSION['flash']['success'] = "Agence créée avec succès.";
        } else {
            $_SESSION['flash']['error'] = "Erreur lors de la création. Elle existe peut-être déjà.";
        }
        $this->redirect('/admin/agencies');
    }

    public function agencyEdit($id)
    {
        $id = (int)$id; // Sécurité ID
        $this->checkAdmin();
        $agencyModel = new Agency();
        $agency = $agencyModel->find($id);
        if (!$agency) {
            $_SESSION['flash']['error'] = "Agence introuvable.";
            $this->redirect('/admin/agencies');
            return;
        }
        $this->render('admin/agencies/edit', ['agency' => $agency]);
    }

    public function agencyUpdate($id)
    {
        $id = (int)$id; // Sécurité ID
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/agencies');
            return;
        }

        $this->verifyCsrfToken(); // AJOUT SÉCURITÉ

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['flash']['error'] = "Le nom de l'agence est requis.";
            $this->redirect('/admin/agencyEdit/' . $id);
            return;
        }

        $agencyModel = new Agency();
        if ($agencyModel->update($id, $name)) {
            $_SESSION['flash']['success'] = "Agence mise à jour avec succès.";
        } else {
            $_SESSION['flash']['error'] = "Erreur lors de la mise à jour de l'agence.";
        }
        $this->redirect('/admin/agencies');
    }

    public function agencyDelete($id)
    {
        $id = (int)$id; // Sécurité ID
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/agencies');
            return;
        }

        $this->verifyCsrfToken(); // AJOUT SÉCURITÉ

        $agencyModel = new Agency();
        if ($agencyModel->delete($id)) {
            $_SESSION['flash']['success'] = "Agence supprimée avec succès.";
        } else {
            $_SESSION['flash']['error'] = "Erreur lors de la suppression de l'agence.";
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
        $id = (int)$id; // Sécurité ID
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/trips');
            return;
        }

        $this->verifyCsrfToken(); // AJOUT SÉCURITÉ

        $tripModel = new Trip();
        if ($tripModel->delete($id)) {
            $_SESSION['flash']['success'] = "Trajet supprimé avec succès.";
        } else {
            $_SESSION['flash']['error'] = "Erreur lors de la suppression du trajet.";
        }
        $this->redirect('/admin/trips');
    }
}
?>