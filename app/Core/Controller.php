<?php
namespace App\Core;

class Controller
{
    protected function render($view, $data = [])
    {
        // Récupérer les messages flash
        $data['flash_success'] = $this->getFlash('success');
        $data['flash_error']   = $this->getFlash('error');

        extract($data);
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
        
        // On le compare avec celui de la session en utilisant hash_equals (pour éviter les attaques temporelles)
        if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            die("Erreur de sécurité : Jeton CSRF invalide ou expiré. Veuillez rafraîchir la page.");
        }
    }
}

?>