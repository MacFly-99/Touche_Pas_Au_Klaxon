# 🚗 Touche pas au klaxon – Application de covoiturage intranet

![Déploiement](https://img.shields.io/badge/D%C3%A9ploiement-Render-blue)
![Base de données](https://img.shields.io/badge/Base_de_donn%C3%A9es-TiDB_Cloud-brightgreen)

🌐 **Application en ligne** : <https://touche-pas-au-klaxon-s080.onrender.com>

---

## 📋 Description

Application web de covoiturage interne développée en **PHP** (architecture **MVC**) pour une entreprise multi-sites. L'accès est strictement réservé aux collaborateurs authentifiés.

### ✨ Fonctionnalités

- **Portail Intranet** : accès sécurisé par formulaire de connexion.
- **Gestion des trajets** :
  - Consultation des trajets disponibles (via des cartes responsives).
  - Affichage des détails en modale (AJAX).
  - Création, modification et suppression d'un trajet (réservé à l'auteur).
- **Administration** :
  - Gestion des utilisateurs (Création, Consultation, Soft Delete).
  - Modification des rôles (Promouvoir / Rétrograder un utilisateur).
  - Gestion des agences (CRUD complet).
  - Supervision et suppression de tous les trajets.
- **Profil** : modification sécurisée de son propre mot de passe.
- **Design** : interface 100% responsive (Bootstrap 5), navigation mobile (menu burger), icônes et alertes dynamiques.

---

## 🛠️ Installation (développement local)

### 📌 Prérequis

- XAMPP (Apache + MySQL)
- PHP 8.3 ou supérieur
- Composer
- Node.js (npm) *(optionnel)*

### 🔧 Étapes

#### 1️⃣ Cloner le projet

Dans le dossier `htdocs` de XAMPP :

```bash
git clone https://github.com/MacFly-99/Touche_Pas_Au_Klaxon.git covoiturage
```

#### 2️⃣ Démarrer XAMPP

Lancer **Apache** et **MySQL** depuis le panneau de contrôle XAMPP.

#### 3️⃣ Configuration de l'environnement

Dupliquer le fichier `.env.example` (ou créer un fichier `.env` à la racine) et configurer vos accès MySQL ainsi que le mot de passe du Super Admin.

#### 4️⃣ Base de données et données de test

1. Importer `sql/create_database.sql` dans phpMyAdmin *(crée la base de dev ET la base de test)*.
2. Dans le terminal, lancer le script de peuplement :

```bash
php seed.php
```

> Ce script génère des mots de passe aléatoires pour les utilisateurs de test et crée un fichier `identifiants_test.txt` avec tous les accès.

#### 5️⃣ Installer les dépendances

```bash
composer install
```

#### 6️⃣ Accéder à l'application

Ouvrir un navigateur : <http://localhost/covoiturage/public/>

---

## ☁️ Déploiement (production)

L'application est déployée avec une stack moderne « Cloud Native ».

🌐 **URL de production** : <https://touche-pas-au-klaxon-s080.onrender.com>

### 🗄️ 1. Base de données (TiDB Cloud)

- **Hébergement** : TiDB Cloud (plan Serverless gratuit, compatible MySQL).
- **Sécurité** : la connexion est chiffrée en SSL. Le certificat racine `isrgrootx1.pem` (Let's Encrypt) est fourni dans le projet et utilisé par PDO (`MYSQL_ATTR_SSL_CA`).
- **Port** : `4000` (port par défaut de TiDB Serverless).

### 🐳 2. Application (Render)

- **Hébergement** : Render (Web Service).
- **Conteneurisation** : l'application est « dockerisée » via un fichier `Dockerfile` (basé sur `php:8.3-apache`).
- **Sécurité (DocumentRoot)** : le `Dockerfile` configure Apache pour pointer directement vers le dossier `/public`, protégeant ainsi les dossiers `app/`, `vendor/` et le fichier `.env`.
- **Variables d'environnement** : les identifiants de la base de données sont injectés de manière sécurisée via le dashboard Render (pas de fichier `.env` en production).

### 🌱 3. Script de peuplement distant

Pour peupler la base distante, il suffit de configurer le `.env` local avec les identifiants TiDB et de lancer :

```bash
php seed.php
```

---

## 🛡️ Sécurité (points forts du projet)

Ce projet intègre les normes de sécurité actuelles pour les applications web :

- **Mots de passe** : hachage robuste via `password_hash()` (Bcrypt).
- **Injections SQL** : 100% des requêtes utilisent des requêtes préparées (PDO).
- **Failles XSS** : échappement systématique des données (`htmlspecialchars`) dans les vues et l'API JSON.
- **Failles CSRF** : génération et vérification de jetons (tokens) uniques sur tous les formulaires POST.
- **Sessions** : cookies HttpOnly, régénération de l'ID après connexion.
- **Soft Delete** : la suppression d'un utilisateur (`deleted_at`) conserve ses trajets en base de données.
- **Headers HTTP** : protection contre le Clickjacking et le MIME Sniffing (`Content-Security-Policy`, `X-Frame-Options`).

---

## 📁 Structure du projet

L'application repose sur une architecture **MVC (Modèle-Vue-Contrôleur)** stricte et sécurisée. L'ensemble du trafic web est redirigé vers un point d'entrée unique (le *Front Controller*).

```text
covoiturage/
├── app/                     # Cœur de l'application (Logique métier)
│   ├── Config/              # Configuration (Database.php avec SSL)
│   ├── Controllers/         # Contrôleurs (Auth, Trip, Admin, Home, Profile)
│   ├── Core/                # Le moteur MVC (Router, Controller, Model)
│   ├── Models/              # Modèles (Interactions BDD)
│   └── Views/               # Vues (layout, home, auth, trip, admin, profile)
│
├── public/                  # ⚠️ SEUL dossier accessible par le navigateur
│   ├── assets/              # Ressources (CSS, JS, Images, Logo)
│   ├── .htaccess            # Réécriture d'URL
│   └── index.php            # Point d'entrée unique (Headers, Session, Routeur)
│
├── sql/                     # Scripts de base de données
│   ├── create_database.sql
│   ├── MCD.png              # Modèle Conceptuel de Données
│   └── MLD.png              # Modèle Logique de Données
│
├── tests/                   # Tests unitaires automatisés (PHPUnit)
│   └── Models/              # AgencyTest, TripTest, UserTest
│
├── vendor/                  # Dépendances externes PHP (Composer)
│
├── .env                     # Variables locales (Non versionné)
├── .env.example             # Modèle de fichier .env à dupliquer
├── .dockerignore            # Fichiers exclus du conteneur Docker
├── .gitignore               # Fichiers exclus de Git
├── .htaccess                # Bloque l'accès aux dossiers sensibles (racine)
├── composer.json            # Dépendances PHP
├── Dockerfile               # Configuration du conteneur (PHP 8.3 + Apache)
├── isrgrootx1.pem           # Certificat SSL Let's Encrypt (TiDB Cloud)
├── phpunit.xml              # Configuration PHPUnit
├── seed.php                 # Script de génération de la BDD de dev
└── README.md                # Documentation du projet
```

### 🗝️ Concepts clés de cette architecture

- **Point d'entrée unique (Front Controller)** : tout passe par `public/index.php`. C'est le routeur qui analyse l'URL et appelle le bon contrôleur.
- **Sécurité du dossier `public/`** : en production (Docker), seul le contenu de ce dossier est exposé. Le reste est totalement inaccessible.
- **Le dossier `Core/`** : framework maison contenant le `Router` et les classes parentes `Controller` (gestion CSRF) et `Model` (connexion PDO).

---

## 🧪 Tests

Les tests unitaires sont réalisés avec **PHPUnit**. Ils sont isolés et autonomes (création de données temporaires via des *Fixtures* avant chaque test).

### ▶️ Exécuter les tests

```bash
vendor/bin/phpunit
```

### ✅ Résultat attendu

| Tests | Assertions | Statut |
| :---: | :--------: | :----: |
| 14    | 37         | ✅ OK  |

---

## 💻 Technologies utilisées

| Technologie         | Rôle                                      |
| ------------------- | ----------------------------------------- |
| **PHP 8.3**         | Back-end (architecture MVC)               |
| **MySQL / TiDB**    | Base de données (local / production)      |
| **Bootstrap 5**     | Interface utilisateur responsive          |
| **Bootstrap Icons** | Iconographie de l'application             |
| **PHPUnit**         | Tests unitaires                           |
| **Docker**          | Conteneurisation pour le déploiement      |
| **Render**          | Hébergement Cloud (PaaS)                  |
| **Composer**        | Gestionnaire de paquets PHP               |

---

## 📊 Modèle de données

### 🧩 MCD (Modèle Conceptuel de Données)

| Association                     | Cardinalités | Lecture                                                                            |
| ------------------------------- | :----------: | ---------------------------------------------------------------------------------- |
| **USER** – publie – **TRIP**    | 0,n / 1,1    | Un utilisateur publie 0 à n trajets ; un trajet a un seul auteur                   |
| **AGENCY** – départ – **TRIP**  | 0,n / 1,1    | Une agence est le départ de 0 à n trajets ; un trajet a une seule agence de départ |
| **AGENCY** – arrivée – **TRIP** | 0,n / 1,1    | Une agence est l'arrivée de 0 à n trajets ; un trajet a une seule agence d'arrivée |

### 🗄️ MLD (Modèle Logique de Données)

Version texte :

```text
agencies (id, name)
users (id, last_name, first_name, phone, email, password_hash, is_admin, deleted_at)
trips (id, departure_agency_id, arrival_agency_id, departure_datetime, arrival_datetime, total_seats, available_seats, user_id)
```

### 🔗 Clés étrangères

Toutes les clés étrangères sont définies avec `ON DELETE CASCADE` :

- `trips.departure_agency_id` → `agencies.id`
- `trips.arrival_agency_id` → `agencies.id`
- `trips.user_id` → `users.id`

> ℹ️ Un utilisateur supprimé via le **Soft Delete** (`deleted_at`) conserve ses trajets. Un `DELETE` définitif en base supprimerait ses trajets (cascade).

---

## 🧑‍💻 Auteur

Développé par **Mattéo Ventura** – Projet CEF (MVC PHP).

---

## 📝 Licence

Projet propriétaire – usage interne uniquement.