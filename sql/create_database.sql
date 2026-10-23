-- ==========================================
-- 1. CRÉATION DE LA BASE DE DÉVELOPPEMENT
-- ==========================================
CREATE DATABASE IF NOT EXISTS covoiturage CHARACTER SET utf8 COLLATE utf8_general_ci;
USE covoiturage;

CREATE TABLE IF NOT EXISTS agencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    deleted_at DATETIME NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS trips (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ==========================================
-- 2. CRÉATION DE LA BASE DE TEST (PHPUNIT)
-- ==========================================
CREATE DATABASE IF NOT EXISTS covoiturage_test CHARACTER SET utf8 COLLATE utf8_general_ci;
USE covoiturage_test;

CREATE TABLE IF NOT EXISTS agencies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    deleted_at DATETIME NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS trips (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ==========================================
-- 3. REMPLIR LA BASE DE TEST AVEC DES DONNÉES
-- ==========================================
USE covoiturage_test;

-- Ajout de 2 agences
INSERT IGNORE INTO agencies (name) VALUES ('Paris'), ('Lyon');

-- Ajout d'un utilisateur (mot de passe : secret)
INSERT IGNORE INTO users (last_name, first_name, phone, email, password_hash, is_admin) 
VALUES ('Martin', 'Alexandre', '0612345678', 'alexandre.martin@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0);

-- Ajout d'un trajet
INSERT IGNORE INTO trips (departure_agency_id, arrival_agency_id, departure_datetime, arrival_datetime, total_seats, available_seats, user_id) 
VALUES (1, 2, '2026-11-01 09:00:00', '2026-11-01 11:00:00', 3, 3, 1);