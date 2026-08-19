<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

session_start();

// Définition de l'URL de base de l'application
define('BASE_URL', '/covoiturage/public');

define('ROOT', dirname(__DIR__));

$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);

?>