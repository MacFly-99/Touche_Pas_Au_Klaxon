<?php
namespace App\Config;

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $host = 'localhost';
        $dbname = 'covoiturage';
        $user = 'root';
        $pass = '';
        $this->pdo = new \PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}

?>