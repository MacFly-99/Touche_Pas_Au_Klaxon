<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

// ==========================================
// 1. MASQUER LES ERREURS (Sécurité)
// ==========================================
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
// Masquer la version de PHP aux yeux du monde
header_remove('X-Powered-By');

// ==========================================
// 2. EN-TÊTES DE SÉCURITÉ HTTP
// ==========================================
// Anti-Clickjacking
header("X-Frame-Options: DENY");
// Anti-MIME Sniffing
header("X-Content-Type-Options: nosniff");
// Politique de référent
header("Referrer-Policy: strict-origin-when-cross-origin");
// Content Security Policy (CSP) - Autorise Bootstrap CDN
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net; connect-src 'self' https://cdn.jsdelivr.net;");

// ==========================================
// 3. SÉCURISATION DES SESSIONS
// ==========================================
ini_set('session.cookie_httponly', 1); 
ini_set('session.use_only_cookies', 1); 
session_start();

// ==========================================
// 4. GÉNÉRATION DU TOKEN CSRF
// ==========================================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ==========================================
// 5. CONSTANTES ET ROUTAGE
// ==========================================
// Définition de l'URL de base de l'application
define('BASE_URL', '/covoiturage/public');
define('ROOT', dirname(__DIR__));

// Lancement du routeur
$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);
?>