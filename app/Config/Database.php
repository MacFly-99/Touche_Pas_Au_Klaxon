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
                $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
                $dotenv->load();

                $host = $_ENV['DB_HOST'];
                $db   = $_ENV['DB_NAME'];
                $user = $_ENV['DB_USER'];
                $pass = $_ENV['DB_PASS'];
                $port = $_ENV['DB_PORT'] ?? '4000'; 

                // On pointe vers le certificat qu'on a ajouté à la racine
                $caPath = dirname(__DIR__, 2) . '/isrgrootx1.pem';

                $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";

                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    // La vérification stricte est réactivée par défaut.
                    // On fournit le chemin de notre certificat CA.
                    PDO::MYSQL_ATTR_SSL_CA => $caPath,
                ]);

            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        
        return self::$instance;
    }
}