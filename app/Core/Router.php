<?php
namespace App\Core;

class Router
{
    public function dispatch($uri)
    {
        // Enlever les paramètres GET
        $uri = strtok($uri, '?');

        // Retirer le préfixe du chemin de base (le dossier public)
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = trim($uri, '/');

        // Défaut
        $controllerName = 'Home';
        $actionName = 'index';

        $parts = explode('/', $uri);
        if (!empty($parts[0])) {
            $controllerName = ucfirst($parts[0]);
        }
        if (!empty($parts[1])) {
            $actionName = $parts[1];
        }
        $params = array_slice($parts, 2);

        $controllerClass = 'App\\Controllers\\' . $controllerName . 'Controller';
        if (!class_exists($controllerClass)) {
            http_response_code(404);
            echo "Controller not found";
            return;
        }

        $controller = new $controllerClass();
        if (!method_exists($controller, $actionName)) {
            http_response_code(404);
            echo "Action not found";
            return;
        }

        $controller->$actionName(...$params);
    }
}

?>