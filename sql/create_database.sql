-- Supprime la base si elle existe déjà (pour repartir à zéro)
DROP DATABASE IF EXISTS covoiturage;

-- Crée la base avec l'encodage UTF-8
CREATE DATABASE covoiturage CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Utilise la base
USE covoiturage;

-- Table des agences
CREATE TABLE agences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table des employés
CREATE TABLE employes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,   -- hash SHA256
    est_admin TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table des trajets
CREATE TABLE trajets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agence_depart_id INT NOT NULL,
    agence_arrivee_id INT NOT NULL,
    gdh_depart DATETIME NOT NULL,
    gdh_arrivee DATETIME NOT NULL,
    nb_places_total INT NOT NULL,
    nb_places_disponibles INT NOT NULL,
    employe_id INT NOT NULL,
    FOREIGN KEY (agence_depart_id) REFERENCES agences(id) ON DELETE CASCADE,
    FOREIGN KEY (agence_arrivee_id) REFERENCES agences(id) ON DELETE CASCADE,
    FOREIGN KEY (employe_id) REFERENCES employes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;