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

    // Affiche le formulaire de création d'utilisateur
    public function userCreate()
    {
        $this->checkAdmin();
        $this->render('admin/users/create');
    }

    // Traite la création de l'utilisateur
    public function userStore()
    {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
            return;
        }

        // 1. Vérification CSRF
        $this->verifyCsrfToken();

        // 2. Récupération et nettoyage des données
        $lastName = trim($_POST['last_name'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $isAdmin = (int)($_POST['is_admin'] ?? 0);

        // 3. Validation
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
        
        // Vérifier si l'email existe déjà
        if ($userModel->findByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé par un autre compte.";
        }

        // S'il y a des erreurs, on renvoie le formulaire avec les données saisies
        if (!empty($errors)) {
            $this->render('admin/users/create', [
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        // 4. Hachage du mot de passe (TRÈS IMPORTANT)
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // 5. Insertion en base
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

        // 6. Redirection
        $this->redirect('/admin/users');
    }

        // Change le rôle d'un utilisateur (Admin <-> User)
    public function userToggleRole($id)
    {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
            return;
        }

        // 1. Vérification CSRF
        $this->verifyCsrfToken();

        // 2. Empêcher l'admin de changer son propre rôle
        if ($id == $_SESSION['user']['id']) {
            $_SESSION['flash']['error'] = "Vous ne pouvez pas changer votre propre rôle.";
            $this->redirect('/admin/users');
            return;
        }

        // 3. Récupérer l'utilisateur cible
        $userModel = new User();
        $user = $userModel->findById($id);

        if (!$user) {
            $_SESSION['flash']['error'] = "Utilisateur introuvable.";
            $this->redirect('/admin/users');
            return;
        }

        // 4. Inverser le rôle (si 1 -> 0, si 0 -> 1)
        $newRole = $user['is_admin'] == 1 ? 0 : 1;

        // 5. Mettre à jour en base
        if ($userModel->updateRole($id, $newRole)) {
            $roleName = $newRole == 1 ? "Administrateur" : "Utilisateur standard";
            $_SESSION['flash']['success'] = "Le rôle de " . htmlspecialchars($user['first_name']) . " a été changé en : $roleName.";
        } else {
            $_SESSION['flash']['error'] = "Erreur lors de la modification du rôle.";
        }

        $this->redirect('/admin/users');
    }

    // --- Suppression d'un utilisateur ---
    public function userDelete($id)
    {
        $this->checkAdmin();
        
        // Vérification de la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/users');
            return;
        }

            $this->verifyCsrfToken(); 

        // Empêcher l'admin de supprimer son propre compte
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