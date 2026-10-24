<?php
namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            try {
                // Fonction helper pour récupérer les variables d'env (ton code actuel)
                $env = function($key, $default = null) {
                    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
                        return $_ENV[$key];
                    }
                    $value = getenv($key);
                    return ($value !== false && $value !== '') ? $value : $default;
                };

                $host = $env('DB_HOST');
                $db   = $env('DB_NAME');
                $user = $env('DB_USER');
                $pass = $env('DB_PASS');
                
                // On récupère le port. Par défaut 3306 pour le local.
                $port = $env('DB_PORT', '3306'); 

                if (!$host || !$db || !$user) {
                    throw new PDOException('Variables DB_HOST, DB_NAME ou DB_USER manquantes.');
                }

                $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

                // Options PDO de base
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                // =====================================================================
                // DÉTECTION DE L'ENVIRONNEMENT POUR LE SSL
                // Si on est en local (localhost), on n'active pas le SSL.
                // Si on est sur TiDB (distant), on active le SSL avec le certificat.
                // =====================================================================
                if ($host !== 'localhost' && $host !== '127.0.0.1') {
                    $caPath = dirname(__DIR__, 2) . '/isrgrootx1.pem';
                    if (file_exists($caPath)) {
                        $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
                    }
                }

                self::$instance = new PDO($dsn, $user, $pass, $options);

            } catch (PDOException $e) {
                // Si on est dans un navigateur (pas en ligne de commande)
                if (php_sapi_name() !== 'cli') {
                    http_response_code(500);
                }
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        
        return self::$instance;
    }
}
?>