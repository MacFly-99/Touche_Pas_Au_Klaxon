<?php
namespace App\Core;

class Controller
{
    protected function render($view, $data = [])
    {
        // Récupérer les messages flash
        $data['flash_success'] = $this->getFlash('success');
        $data['flash_error']   = $this->getFlash('error');

        // SÉCURITÉ : Empêcher extract() d'écraser des variables critiques
        $protected = ['view', 'data', 'this', 'protected'];
        foreach ($data as $key => $value) {
            if (!in_array($key, $protected)) {
                $$key = $value;
            }
        }

        include ROOT . '/app/Views/layout/header.php';
        include ROOT . '/app/Views/' . $view . '.php';
        include ROOT . '/app/Views/layout/footer.php';
    }

    protected function redirect($url)
    {
        // Si l'URL ne commence pas par http:// ou https://, on ajoute BASE_URL
        if (!preg_match('#^https?://#', $url)) {
            $url = BASE_URL . $url;
        }
        header('Location: ' . $url);
        exit;
    }

    protected function setFlash($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash($key)
    {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    /**
     * Vérifie la validité du token CSRF
     * Si invalide, arrête le script avec une erreur 403
     */
    protected function verifyCsrfToken()
    {
        // On récupère le token envoyé par le formulaire
        $token = $_POST['csrf_token'] ?? '';
        
        // SÉCURITÉ : On vérifie que le token existe en session AVANT de comparer
        if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            die("Erreur de sécurité : Jeton CSRF invalide ou expiré. Veuillez rafraîchir la page.");
        }
    }
}
?>