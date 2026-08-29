# 🚗 Touche pas au klaxon – Application de covoiturage intranet

## 📋 Description

Application web de covoiturage interne développée en PHP (architecture MVC) pour une entreprise multi-sites.

**Fonctionnalités :**
- Consultation des trajets disponibles (sans connexion)
- Connexion / Déconnexion (email + mot de passe)
- Création, modification et suppression d'un trajet (pour l'auteur)
- Affichage des détails en modale (avec infos de l'auteur)
- Administration :
  - Gestion des utilisateurs (consultation)
  - Gestion des agences (CRUD complet)
  - Suppression des trajets (admin)

---

## 🛠️ Installation

### Prérequis
- XAMPP (Apache + MySQL)
- PHP 7.4 ou supérieur
- Composer
- Node.js (npm)

### 1. Cloner le projet
```bash
git clone https://github.com/votre-utilisateur/Touche_Pas_Au_Klaxon.git

2. Démarrer XAMPP
Lancer Apache et MySQL depuis le panneau de contrôle XAMPP.

3. Base de données
Ouvrir phpMyAdmin : http://localhost/phpmyadmin

Importer sql/create_database.sql puis sql/seed_data.sql

4. Installer les dépendances PHP
composer install

5. Installer les dépendances front-end (Bootstrap, Sass)
npm install

6. Compiler le Sass (optionnel – on utilise le CDN)
npm run build

7. Configurer l'URL de base
Ouvrir public/index.php
Vérifier que BASE_URL est bien définie :

define('BASE_URL', '/covoiturage/public');

8. Accéder à l'application
Ouvrir un navigateur : http://localhost/covoiturage/public/


🔐 Identifiants de test

Rôle	        Email	                    Mot de passe
Utilisateur	    alexandre.martin@email.fr	secret
Administrateur	admin@covoiturage.fr	    secret


📁 Structure du projet

covoiturage/
├── app/
│   ├── Config/          # Configuration (base de données)
│   ├── Controllers/     # Contrôleurs (Auth, Trip, Admin, Home)
│   ├── Core/            # Noyau MVC (Router, Controller, Model)
│   ├── Models/          # Modèles (User, Agency, Trip)
│   └── Views/           # Vues (layout, home, auth, trip, admin)
├── public/
│   ├── assets/          # Fichiers SCSS et CSS
│   ├── .htaccess        # Réécriture d'URL
│   └── index.php        # Point d'entrée unique
├── sql/
│   ├── create_database.sql
│   └── seed_data.sql
├── tests/               # Tests unitaires PHPUnit
├── vendor/              # Dépendances PHP (généré par Composer)
├── node_modules/        # Dépendances JS (généré par npm)
├── .gitignore
├── composer.json
├── package.json
├── phpunit.xml
├── README.md
└── .htaccess


🧪 Exécuter les tests

vendor/bin/phpunit
Tous les tests doivent passer avec succès.


🖥️ Technologies utilisées

PHP (Back-end – architecture MVC)

MySQL / MariaDB (Base de données)

Bootstrap 5 (Interface utilisateur – CDN)

Sass (Gestion des couleurs imposées – optionnel)

PHPUnit (Tests unitaires)

Composer (Gestionnaire de paquets PHP)

npm (Gestionnaire de paquets JavaScript)


📊 Modèle de données

MCD (Modèle Conceptuel de Données)
Fichier présent au format PNG dans le dossier sql de l'application : MCD.png


MLD (Modèle Logique de Données)
Fichier présent au format TXT dans le dossier sql de l'application : MLD.txt

agencies (id, name)
users (id, last_name, first_name, phone, email, password_hash, is_admin)
trips (id, departure_agency_id, arrival_agency_id, departure_datetime, arrival_datetime, total_seats, available_seats, user_id)

Clés étrangères :

trips.departure_agency_id -> agencies.id
trips.arrival_agency_id -> agencies.id
trips.user_id -> users.id


👨‍💻 Auteur

Développé par Mattéo Ventura dans le cadre d'un projet CEF – MVC PHP.


📝 Licence

Projet propriétaire – usage interne uniquement.