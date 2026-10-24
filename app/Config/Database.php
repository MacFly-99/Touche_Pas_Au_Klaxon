<?php
namespace App\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            try {
                $root = dirname(__DIR__, 2);

                // En local : charge le fichier .env.
                // Sur Render : pas de .env, safeLoad() l'ignore sans planter
                // et les variables d'environnement de Render sont utilisées.
                Dotenv::createImmutable($root)->safeLoad();

                // Lit d'abord $_ENV, puis getenv() (variables Render), sinon la valeur par défaut
                $env = function (string $key, $default = null) {
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
                $port = $env('DB_PORT', '4000');

                if (!$host || !$db || !$user) {
                    throw new PDOException('Variables DB_HOST, DB_NAME ou DB_USER manquantes.');
                }

                // Certificat CA (Let's Encrypt / ISRG Root X1) à la racine du projet
                $caPath = $root . '/isrgrootx1.pem';

                $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    // Connexion SSL obligatoire pour TiDB Cloud
                    PDO::MYSQL_ATTR_SSL_CA       => $caPath,
                ]);

            } catch (PDOException $e) {
                // Le détail va dans les logs Render, pas à l'écran du visiteur
                error_log('Erreur de connexion BDD : ' . $e->getMessage());
                http_response_code(500);
                die('Erreur de connexion à la base de données.');
            }
        }

        return self::$instance;
    }
}