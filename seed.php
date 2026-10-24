<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;

// 1. Charger le .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$db   = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$port = $_ENV['DB_PORT'] ?? '4000'; // Port par défaut de TiDB Cloud

// Configuration SSL pour TiDB (indispensable pour le plan serverless)
$caPath = __DIR__ . '/isrgrootx1.pem';
if (!file_exists($caPath)) {
    die("❌ Erreur : Le fichier de certificat isrgrootx1.pem est introuvable à la racine.\n");
}

try {
    // 2. Connexion et création de la base (avec SSL)
    // On ajoute le port et les options SSL
    $dsn = "mysql:host=$host;port=$port;charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_SSL_CA => $caPath,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

    // TiDB Cloud Serverless interdit parfois le DROP/CREATE DATABASE si on n'est pas connecté à la bonne instance.
    // On essaie de créer la base. Si elle existe déjà, on passe à la suite.
    try {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8 COLLATE utf8_general_ci");
    } catch (PDOException $e) {
        // Si on n'a pas les droits de création, on ignore et on espère qu'elle existe
    }
    
    $pdo->exec("USE $db");

    // 3. Nettoyage (On supprime les tables si elles existent pour repartir de zéro)
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("DROP TABLE IF EXISTS trips");
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("DROP TABLE IF EXISTS agencies");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 4. Création des tables
    $pdo->exec("CREATE TABLE agencies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        last_name VARCHAR(50) NOT NULL,
        first_name VARCHAR(50) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        is_admin TINYINT(1) DEFAULT 0,
        deleted_at DATETIME NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $pdo->exec("CREATE TABLE trips (
        id INT AUTO_INCREMENT PRIMARY KEY,
        departure_agency_id INT NOT NULL,
        arrival_agency_id INT NOT NULL,
        departure_datetime DATETIME NOT NULL,
        arrival_datetime DATETIME NOT NULL,
        total_seats INT NOT NULL,
        available_seats INT NOT NULL,
        user_id INT NOT NULL,
        FOREIGN KEY (departure_agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
        FOREIGN KEY (arrival_agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    echo "✅ Base de données et tables créées.\n";

    // 5. Insertion des agences
    $agencies = ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 'Lille', 'Rennes', 'Reims'];
    $stmtAgency = $pdo->prepare("INSERT INTO agencies (name) VALUES (?)");
    foreach ($agencies as $agency) {
        $stmtAgency->execute([$agency]);
    }
    echo "✅ Agences insérées.\n";

    // 6. Préparation de l'insertion des utilisateurs
    $stmtUser = $pdo->prepare("INSERT INTO users (last_name, first_name, phone, email, password_hash, is_admin) VALUES (?, ?, ?, ?, ?, ?)");
    
    // Tableau pour stocker les identifiants générés
    $credentialsTxt = "=== IDENTIFIANTS DE CONNEXION (BASE DISTANTE) ===\n\n";

    // 6a. Insertion de l'ADMIN via .env
    $adminHash = password_hash($_ENV['ADMIN_PASSWORD'], PASSWORD_DEFAULT);
    $stmtUser->execute([
        $_ENV['ADMIN_LAST_NAME'] ?? 'Admin',
        $_ENV['ADMIN_FIRST_NAME'] ?? 'Super',
        $_ENV['ADMIN_PHONE'] ?? '0102030405',
        $_ENV['ADMIN_EMAIL'],
        $adminHash,
        1
    ]);
    $credentialsTxt .= "👑 ADMIN :\nEmail: {$_ENV['ADMIN_EMAIL']}\nMot de passe: {$_ENV['ADMIN_PASSWORD']}\n\n";
    $credentialsTxt .= "-----------------------------------\n\n";

    // 6b. Les 20 Utilisateurs avec MOTS DE PASSE UNIQUES
    $fakeUsers = [
        ['Martin', 'Alexandre', '0612345678', 'alexandre.martin@email.fr'],
        ['Dubois', 'Sophie', '0698765432', 'sophie.dubois@email.fr'],
        ['Bernard', 'Julien', '0622446688', 'julien.bernard@email.fr'],
        ['Moreau', 'Camille', '0611223344', 'camille.moreau@email.fr'],
        ['Lefevre', 'Lucie', '0777889900', 'lucie.lefevre@email.fr'],
        ['Leroy', 'Thomas', '0655443322', 'thomas.leroy@email.fr'],
        ['Roux', 'Chloé', '0633221199', 'chloe.roux@email.fr'],
        ['Petit', 'Maxime', '0766778899', 'maxime.petit@email.fr'],
        ['Garnier', 'Laura', '0688776655', 'laura.garnier@email.fr'],
        ['Dupuis', 'Antoine', '0744556677', 'antoine.dupuis@email.fr'],
        ['Lefebvre', 'Emma', '0699887766', 'emma.lefebvre@email.fr'],
        ['Fontaine', 'Louis', '0655667788', 'louis.fontaine@email.fr'],
        ['Chevalier', 'Clara', '0788990011', 'clara.chevalier@email.fr'],
        ['Robin', 'Nicolas', '0644332211', 'nicolas.robin@email.fr'],
        ['Gauthier', 'Marine', '0677889922', 'marine.gauthier@email.fr'],
        ['Fournier', 'Pierre', '0722334455', 'pierre.fournier@email.fr'],
        ['Girard', 'Sarah', '0688665544', 'sarah.girard@email.fr'],
        ['Lambert', 'Hugo', '0611223366', 'hugo.lambert@email.fr'],
        ['Masson', 'Julie', '0733445566', 'julie.masson@email.fr'],
        ['Henry', 'Arthur', '0666554433', 'arthur.henry@email.fr']
    ];

    foreach ($fakeUsers as $u) {
        // Générer un mot de passe aléatoire
        $randomPassword = substr(str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 10);
        $hash = password_hash($randomPassword, PASSWORD_DEFAULT);
        
        $stmtUser->execute([$u[0], $u[1], $u[2], $u[3], $hash, 0]);
        
        $credentialsTxt .= "Utilisateur : {$u[1]} {$u[0]}\nEmail: {$u[3]}\nMot de passe: $randomPassword\n\n";
    }
    
    echo "✅ 20 Utilisateurs créés avec des mots de passe uniques.\n";

    // 7. Insertion de trajets (pour Alexandre Martin)
    $stmt = $pdo->query("SELECT id FROM users WHERE email = 'alexandre.martin@email.fr'");
    $userId = $stmt->fetchColumn();

    $trips = [
        [1, 2, date('Y-m-d H:i:s', strtotime('+2 days')), date('Y-m-d H:i:s', strtotime('+2 days + 2 hours')), 3, 3, $userId],
        [2, 3, date('Y-m-d H:i:s', strtotime('+3 days')), date('Y-m-d H:i:s', strtotime('+3 days + 1 hour')), 4, 4, $userId],
        [1, 9, date('Y-m-d H:i:s', strtotime('+4 days')), date('Y-m-d H:i:s', strtotime('+4 days + 3 hours')), 2, 2, $userId],
    ];
    $stmtTrip = $pdo->prepare("INSERT INTO trips (departure_agency_id, arrival_agency_id, departure_datetime, arrival_datetime, total_seats, available_seats, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($trips as $trip) {
        $stmtTrip->execute($trip);
    }
    echo "✅ Trajets insérés (dates futures).\n";

    // 8. Sauvegarder le fichier texte des identifiants
    file_put_contents('identifiants_test.txt', $credentialsTxt);
    
    echo "\n🎉 TERMINÉ ! La base de données distante est prête.\n";
    echo "👉 Ouvre le fichier 'identifiants_test.txt' à la racine pour voir les accès !\n";

} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage() . "\n");
}