<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

// ==========================================
// 0. CHARGEMENT DU FICHIER .ENV
// ==========================================
// On lit le fichier .env situé à la racine du projet
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignorer les commentaires
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Séparer la clé et la valeur
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Supprimer les guillemets éventuels autour de la valeur
            $value = trim($value, '"\''); 
            
            // Définir la variable d'environnement si elle n'existe pas déjà
            if (!getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// ==========================================
// 1. MASQUER LES ERREURS (Sécurité)
// ==========================================
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
// Masquer la version de PHP aux yeux du monde
header_remove('X-Powered-By');

// ==========================================
// 2. SÉCURISATION DES SESSIONS
// ==========================================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');

// Détection de l'environnement (Local ou Production)
$isLocal = isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1');

if (!$isLocal) {
    // On force le cookie Secure UNIQUEMENT en production (sur Render)
    ini_set('session.cookie_secure', 1); 
}

session_start();

// ==========================================
// 3. GÉNÉRATION DU TOKEN CSRF
// ==========================================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ==========================================
// 4. CONSTANTES ET ROUTAGE
// ==========================================
if ($isLocal) {
    // Environnement local (XAMPP)
    define('BASE_URL', '/covoiturage/public');
} else {
    // Environnement de production (Render)
    define('BASE_URL', ''); 
}

define('ROOT', dirname(__DIR__));

// Lancement du routeur
$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);
?>