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
}

?>