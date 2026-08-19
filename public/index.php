<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

session_start();

define('ROOT', dirname(__DIR__));

$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI']);

?>