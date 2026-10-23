<?php
namespace App\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private static $instance = null;

    // La méthode magique qui manquait !
    public static function getInstance()
    {
        if (self::$instance === null) {
            try {
                // Chargement du fichier .env (situé à la racine du projet)
                // dirname(__DIR__, 2) remonte depuis app/Config/ vers le dossier racine
                $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
                $dotenv->load();

                $host = $_ENV['DB_HOST'];
                $db   = $_ENV['DB_NAME'];
                $user = $_ENV['DB_USER'];
                $pass = $_ENV['DB_PASS'];

                $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
                
                // Connexion PDO
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);

            } catch (PDOException $e) {
                // En cas d'erreur de connexion
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        
        return self::$instance;
    }
}