# 🚗 Touche pas au klaxon – Application de covoiturage intranet

![Déploiement](https://img.shields.io/badge/D%C3%A9ploiement-Render-blue)
![Base de données](https://img.shields.io/badge/Base_de_donn%C3%A9es-TiDB_Cloud-brightgreen)
![Sécurité](https://img.shields.io/badge/S%C3%A9curit%C3%A9-Header_A_(Snyk)-success)

🌐 **Application en ligne** : <https://touche-pas-au-klaxon-s080.onrender.com>

---

## 📋 Description

Application web de covoiturage interne développée en **PHP** (architecture **MVC**) pour une entreprise multi-sites. L'accès est strictement réservé aux collaborateurs authentifiés.

### ✨ Fonctionnalités

- **Portail Intranet** : accès sécurisé par formulaire de connexion.
- **Gestion des trajets** :
  - Consultation des trajets disponibles (cartes responsives).
  - Affichage des détails en modale (AJAX sécurisé contre les XSS).
  - Création, modification et suppression d'un trajet (réservé à l'auteur).
- **Administration** :
  - Gestion des utilisateurs (création, consultation, Soft Delete).
  - Modification des rôles (promouvoir / rétrograder un utilisateur).
  - Gestion des agences (CRUD complet).
  - Supervision et suppression de tous les trajets.
- **Profil** : modification sécurisée de son propre mot de passe.
- **Pages légales** : mentions légales et Conditions Générales de Vente (CGV) intégrées.
- **Design** : interface 100 % responsive (Bootstrap 5), navigation mobile (menu burger), icônes et alertes dynamiques.

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

#### 3️⃣ Configurer l'environnement

Dupliquer le fichier `.env.example` (ou créer un fichier `.env` à la racine) et renseigner vos accès MySQL.

> **Note :** le fichier `.env` est lu nativement par `public/index.php` (aucune librairie externe requise).

#### 4️⃣ Base de données et données de test

1. Importer `sql/create_database.sql` dans phpMyAdmin (crée la base de développement **et** la base de test).
2. Lancer le script de peuplement depuis le terminal :

   ```bash
   php seed.php
   ```

   Ce script génère des mots de passe aléatoires pour les utilisateurs de test et crée un fichier local `identifiants_test.txt` (ignoré par Git).

#### 5️⃣ Installer les dépendances

```bash
composer install
```

#### 6️⃣ Accéder à l'application

Ouvrir un navigateur à l'adresse : <http://localhost/covoiturage/public/>

---

## ☁️ Déploiement (production)

L'application est déployée avec une stack moderne « Cloud Native ».

🌐 **URL de production** : <https://touche-pas-au-klaxon-s080.onrender.com>

### 🗄️ 1. Base de données (TiDB Cloud)

- **Hébergement** : TiDB Cloud (plan Serverless gratuit, compatible MySQL).
- **Sécurité** : connexion chiffrée en SSL. Le certificat racine `isrgrootx1.pem` (Let's Encrypt) est fourni dans le projet et utilisé par PDO (`MYSQL_ATTR_SSL_CA`).
- **Port** : `4000` (port par défaut de TiDB Serverless).

### 🐳 2. Application (Render)

- **Hébergement** : Render (Web Service).
- **Conteneurisation** : l'application est « dockerisée » via un `Dockerfile` (basé sur `php:8.3-apache`).
- **Sécurité (DocumentRoot & Apache)** : le `Dockerfile` configure Apache pour pointer directement vers le dossier `/public`, ce qui protège les dossiers `app/`, `vendor/` et le fichier `.env`. Les modules `rewrite` et `headers` d'Apache sont activés pour prendre en charge les règles de sécurité du `.htaccess`.
- **Variables d'environnement** : les identifiants de la base de données sont injectés de manière sécurisée via le dashboard Render (pas de fichier `.env` en production).

### 🌱 3. Script de peuplement distant

Pour peupler la base distante, configurer le `.env` local avec les identifiants TiDB, puis lancer :

```bash
php seed.php
```

---

## 🛡️ Sécurité (points forts du projet)

Ce projet a été audité et intègre les normes de sécurité actuelles (OWASP Top 10) pour les applications web. Il obtient la note maximale **A** sur Security Headers (Snyk).

| Domaine | Mesures mises en place |
|---|---|
| **Mots de passe** | Hachage robuste via `password_hash()` (Bcrypt). |
| **Injections SQL** | 100 % des requêtes sont préparées (PDO avec `EMULATE_PREPARES => false`). |
| **Failles XSS** | Échappement systématique des données (`htmlspecialchars`) côté PHP ; `textContent` / fonction d'échappement côté JavaScript (AJAX). |
| **Failles CSRF** | Génération et vérification de jetons uniques sur tous les formulaires POST (y compris l'administration). |
| **Sessions** | Cookies `HttpOnly`, `SameSite=Lax`, `Secure` (en production) ; régénération de l'ID après connexion (`session_regenerate_id`) ; suppression du cookie à la déconnexion. |
| **Headers HTTP** | Protection contre le Clickjacking et le MIME Sniffing, forçage du HTTPS : `Strict-Transport-Security`, `X-Frame-Options`, `X-Content-Type-Options`, `Content-Security-Policy`, `Referrer-Policy`, `Permissions-Policy`. |
| **Configuration serveur** | Les fichiers `.htaccess` bloquent l'accès aux fichiers sensibles (`.env`, `.git`, etc.) et masquent la structure interne. |
| **Soft Delete** | La suppression d'un utilisateur (`deleted_at`) conserve ses trajets en base de données. |

---

## 🗝️ Concepts clés de l'architecture

- **Point d'entrée unique (Front Controller)** : tout passe par `public/index.php`. Le routeur analyse l'URL et appelle le bon contrôleur.
- **Sécurité du dossier `public/`** : en production (Docker), seul le contenu de ce dossier est exposé. Le reste est totalement inaccessible.
- **Dossier `Core/`** : framework maison contenant le `Router` et les classes parentes `Controller` (gestion CSRF) et `Model` (connexion PDO).

---

## 🧪 Tests

Les tests unitaires sont réalisés avec **PHPUnit**. Ils sont isolés et autonomes (création de données temporaires via des Fixtures avant chaque test).

### ▶️ Exécuter les tests

```bash
vendor/bin/phpunit
```

### ✅ Résultat attendu

| Tests | Assertions | Statut |
|:---:|:---:|:---:|
| 14 | 37 | ✅ OK |

---

## 💻 Technologies utilisées

| Technologie | Rôle |
|---|---|
| PHP 8.3 | Back-end (architecture MVC) |
| MySQL / TiDB | Base de données (local / production) |
| Bootstrap 5 | Interface utilisateur responsive |
| Bootstrap Icons | Iconographie de l'application |
| PHPUnit | Tests unitaires |
| Docker | Conteneurisation pour le déploiement |
| Render | Hébergement Cloud (PaaS) |
| Composer | Gestionnaire de paquets PHP |

---

## 📊 Modèle de données

### 🧩 MCD (Modèle Conceptuel de Données)

| Association | Cardinalités | Lecture |
|---|:---:|---|
| USER – publie – TRIP | 0,n / 1,1 | Un utilisateur publie 0 à n trajets ; un trajet a un seul auteur. |
| AGENCY – départ – TRIP | 0,n / 1,1 | Une agence est le départ de 0 à n trajets ; un trajet a une seule agence de départ. |
| AGENCY – arrivée – TRIP | 0,n / 1,1 | Une agence est l'arrivée de 0 à n trajets ; un trajet a une seule agence d'arrivée. |

### 🗄️ MLD (Modèle Logique de Données)

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

> ℹ️ Un utilisateur supprimé via le Soft Delete (`deleted_at`) conserve ses trajets. Un `DELETE` définitif en base supprimerait ses trajets (cascade).

---

## 🧑‍💻 Auteur

Développé par **MacFly-99** – (MVC PHP).

## 📝 Licence

Projet propriétaire – usage interne uniquement.