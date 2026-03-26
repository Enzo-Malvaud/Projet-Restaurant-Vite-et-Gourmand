# 🍽️ Vite et Gourmand — Site Web Traiteur
 
Application web et mobile pour l'entreprise de traiteur **Vite et Gourmand**, basée à Bordeaux et tenue par José et Julie. Elle permet la consultation et la commande de menus (simples ou événementiels), et assure la présence en ligne de l'entreprise.
 
---
 
## 📋 Table des matières
 
- [Présentation](#présentation)
- [Lien deploiement](#deploiement)
- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Architecture](#architecture)
- [Prérequis](#prérequis)
- [Installation — Développement](#installation--développement)
- [Variables d'environnement — `.env.local`](#variables-denvironnement--envlocal)
- [Variables d'environnement — `.env.prod`](#variables-denvironnement--envprod)
- [Déploiement en production](#déploiement-en-production)
- [Accès aux services](#accès-aux-services)
- [Structure du projet](#structure-du-projet)
- [Authentification API](#authentification-api)
- [Rôles et permissions](#rôles-et-permissions)
- [Dépendances](#dépendances)
 
---


## Présentation
 
Vite et Gourmand est une plateforme permettant :
 
- La **consultation des menus** (repas simples, menus événementiels : Noël, Pâques, etc.)
- La **commande en ligne** après inscription
- La **location de matériel de restauration**
- La **gestion des avis clients**
- Un **espace personnel** pour les utilisateurs et une interface de gestion pour les employés/administrateurs
 
---

## Lien deploiement

- http://viteetgourmand.ddns.net/

---

## Fonctionnalités
 
### 👤 Visiteur (non connecté)
- Consulter les menus de Vite et Gourmand
- S'informer sur l'entreprise (avis, horaires, coordonnées, mentions légales, CGV)
- Contacter l'entreprise via le formulaire de contact
 
### 🔐 Utilisateur (inscrit)
- Commander un ou plusieurs menus
- Louer du matériel de restauration
- Poster un avis sur une commande ou une location
- Gérer son espace personnel :
  - Informations personnelles
  - Historique des commandes et locations (avec statuts)
  - Annulation / modification d'une commande ou location (tant que le statut n'est pas validé)
  - Gestion des avis postés
 
### 🧑‍🍳 Employé (hérite des droits utilisateur)
- Mise à jour du statut des commandes et des locations
- Modération des avis clients (valider / refuser)
- Gestion du catalogue des menus (insertion, modification, suppression)
 
### 🛠️ Administrateur (hérite des droits employé)
- Analyse des statistiques :
  - Nombre de commandes par menu
  - Tableaux de chiffres d'affaires
  - Filtres par ressource
- Gestion des comptes employés (création, désactivation)

---
 
## Stack technique
 
| Couche | Technologie | |--------|-------------| | Backend | PHP 8.4 + Symfony 8.0 | | ORM / ODM | Doctrine ORM 3.x & MongoDB ODM | | Base de données principale | MySQL 8.0 | | Base de données secondaire | MongoDB 7.0 | | Serveur web | Nginx 1.27 (Alpine) | | Conteneurisation | Docker + Docker Compose | | Sécurité | Symfony Security (token API) | | Documentation API | NelmioApiDocBundle (Swagger) 
 
---
 
## Architecture
 
```
┌─────────────────────────────────────────────────────┐
│                    Docker Network                    │
│                                                      │
│   ┌──────────┐     ┌──────────┐     ┌────────────┐  │
│   │  Frontend│────▶│  Nginx   │────▶│  PHP-FPM   │  │
│   │  (static)│     │  :8080   │     │  (Symfony) │  │
│   └──────────┘     └──────────┘     └─────┬──┬───┘  │
│                                           │  │       │
│                              ┌────────────┘  │       │
│                              ▼               ▼       │
│                        ┌──────────┐   ┌──────────┐  │
│                        │  MySQL   │   │ MongoDB  │  │
│                        │  :3307   │   │  :27017  │  │
│                        └──────────┘   └──────────┘  │
└─────────────────────────────────────────────────────┘
```
 
---

## Prérequis
 
- [Docker](https://www.docker.com/) ≥ 24.x
- [Docker Compose](https://docs.docker.com/compose/) ≥ 2.x
 
---

 
## Installation — Développement
 
### 1. Cloner le dépôt
 
```bash
git clone <url-du-repo>
cd vite-et-gourmand
```
 
### 2. Configurer l'environnement
 
```bash
cp .env.local.example .env.local
# Modifier les valeurs selon votre configuration locale
```
 
### 3. Lancer les services (mode dev avec phpMyAdmin et Mongo Express)
 
```bash
docker compose --profile dev up -d --build
```
 
### 4. Installer les dépendances PHP
 
```bash
docker exec php-container composer install
```
 
### 5. Exécuter les migrations
 
```bash
docker exec php-container php bin/console doctrine:migrations:migrate --no-interaction
```
 
### 6. (Optionnel) Charger les fixtures
 
```bash
docker exec php-container php bin/console doctrine:fixtures:load --no-interaction
```
 
### Arrêter les services
 
```bash
docker compose down
 
# ⚠️ Avec suppression des volumes (supprime toutes les données)
docker compose down -v
```
 
---

 
## Variables d'environnement — `.env.local`
 
Fichier utilisé en **développement local**. Il ne doit jamais être versionné (présent dans `.gitignore`).
 
```dotenv
# ========================================
# .env.local — Développement local
# ========================================
 
# ── APPLICATION ──────────────────────────────────────────────────────────────
 
APP_ENV=dev
# Environnement Symfony : "dev" active le profiler, les logs détaillés
# et les messages d'erreur complets dans le navigateur.
 
APP_DEBUG=true
# Active le mode debug Symfony : affiche les erreurs détaillées côté client.
# Ne jamais mettre à true en production.
 
NODE_ENV=development
# Environnement Node.js si des outils front (Webpack, Vite...) sont utilisés.
 
APP_PORT=3000
# Port utilisé par un éventuel serveur de développement front-end.

DEBUG=true
# Active les logs de debug généraux (utilisé par certaines bibliothèques PHP).

# ── BASE DE DONNÉES MYSQL ─────────────────────────────────────────────────────
 
# Déclaration des briques individuelles
DB_USER=veg_user  (attention caractère spéciaux ne passe pas)
DB_PASSWORD=ton_password_secu
DB_HOST=db
DB_PORT=3307
DB_NAME=db_Vite_et_Gourmand

# Assemblage du DSN 
DATABASE_URL="mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}?serverVersion=8.0.32&charset=utf8mb4"

# ── MONGODB ───────────────────────────────────────────────────────────────────
 
MONGO_ROOT_USER=admin (attention caractère spéciaux ne passe pas)
MONGO_ROOT_PASSWORD=password123
MONGO_DATABASE=myapp
MONGO_PORT=27017
MONGO_HOST=mongodb
# DSN MongoDB interne avec interpolation
MONGODB_URI="mongodb://${MONGO_ROOT_USER}:${MONGO_ROOT_PASSWORD}@${MONGO_HOST}:${MONGO_PORT}/${MONGO_DATABASE}?authSource=admin"

# ── NGINX ─────────────────────────────────────────────────────────────────────
 
NGINX_PORT=8080
# Port exposé sur l'hôte pour accéder à l'application web via Nginx.
# Accès : http://localhost:8080

# ── PHPMYADMIN (développement uniquement) ─────────────────────────────────────
 
PHPMYADMIN_PORT=8081
# Port exposé pour l'interface graphique phpMyAdmin.
# Accès : http://localhost:8081
# Ce service n'est lancé qu'avec le profil Docker "dev".

# ── MONGO EXPRESS (développement uniquement) ──────────────────────────────────
 
MONGO_EXPRESS_PORT=8083
# Port exposé pour l'interface graphique Mongo Express (administration MongoDB).
# Accès : http://localhost:8083
# Ce service n'est lancé qu'avec le profil Docker "dev".

# ── CORS ──────────────────────────────────────────────────────────────────────
 
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
# Regex des origines autorisées pour les requêtes cross-origin (CORS).
# Ici : autorise uniquement localhost (tous ports), en HTTP et HTTPS.
# En production, remplacer par le domaine réel : '^https://www\.viteetgourmand\.fr$'
```
 
---

## Variables d'environnement — `.env.prod`
 
Fichier utilisé en **production**. Il doit être créé directement sur le serveur et ne jamais être versionné ni transféré via un dépôt Git public. On le dépose manuellement via SCP (voir section suivante).

```dotenv
# ========================================
# .env.prod — Production serveur
# ========================================

# ── APPLICATION ──────────────────────────────────────────────────────────────
 
APP_ENV=prod
# Symfony passe en mode production : pas de profiler, pas d'erreurs exposées,
# cache activé et optimisé. Obligatoire en production.
 
APP_DEBUG=false
# Désactive le mode debug. Les erreurs ne sont plus affichées à l'utilisateur,
# elles sont uniquement loggées côté serveur (var/log/prod.log).
 
APP_SECRET=CHANGEZ_MOI_PAR_UNE_CHAINE_ALEATOIRE_DE_32_CHARS
# Clé secrète utilisée par Symfony pour signer les sessions, les tokens CSRF, etc.
# Générer une valeur sécurisée avec : openssl rand -hex 32
# ⚠️ Ne jamais utiliser la valeur par défaut ni la même clé qu'en développement.

# ── BASE DE DONNÉES MYSQL ─────────────────────────────────────────────────────
 
# Assemblage du DSN  
DATABASE_URL="mysql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}?serverVersion=8.0.32&charset=utf8mb4"
# DSN de connexion Doctrine. En production, ne jamais utiliser l'utilisateur root.
# Créer un utilisateur MySQL dédié avec des droits limités à la base de l'application.
# Exemple SQL : CREATE USER 'veg_user'@'%' IDENTIFIED BY 'MOT_DE_PASSE_FORT';
#               GRANT ALL PRIVILEGES ON db_Vite_et_Gourmand.* TO 'veg_user'@'%';
 
# Déclaration des briques individuelles
DB_USER=veg_user  (attention caractère spéciaux ne passe pas)
DB_PASSWORD=ton_password_secu
DB_HOST=db
DB_NAME=db_Vite_et_Gourmand
# Port non exposé sur l'hôte en production

# ── MONGODB ───────────────────────────────────────────────────────────────────

MONGO_ROOT_USER=admin
# Nom d'utilisateur administrateur MongoDB.
 
MONGO_ROOT_PASSWORD=MOT_DE_PASSE_FORT_MONGO
# ⚠️ Utiliser un mot de passe long et aléatoire.
# Générer avec : openssl rand -base64 24
 
MONGO_DATABASE=vite_et_gourmand_prod
# Nom de la base de données MongoDB en production.
# Utiliser un nom différent du dev pour éviter toute confusion.

# En production, ne pas exposer le port sur l'hôte.

 
MONGODB_URI="mongodb://${MONGO_ROOT_USER}:${MONGO_ROOT_PASSWORD}@${MONGO_HOST}:${MONGO_PORT}/${MONGO_DATABASE}?authSource=admin"
# URI de connexion MongoDB pour l'application PHP.

# ── NGINX ─────────────────────────────────────────────────────────────────────
 
NGINX_PORT=80
# En production, Nginx écoute sur le port 80 (HTTP standard).
# Si un certificat SSL (Let's Encrypt) est configuré, utiliser 443.

# ── CORS ──────────────────────────────────────────────────────────────────────
 
CORS_ALLOW_ORIGIN='^https://www\.viteetgourmand\.fr$'
# En production, restreindre strictement aux domaines autorisés.
# ⚠️ Remplacer par le vrai domaine de l'application.
```
 
---
 
## Déploiement en production
 
### Prérequis sur le serveur
- Ubuntu / Debian (ou équivalent Linux)
- Docker ≥ 24.x et Docker Compose ≥ 2.x installés
- Accès SSH avec un utilisateur ayant les droits `sudo` ou appartenant au groupe `docker`
 
---
 
### 1. Préparer le fichier `.env.prod` localement
 
Créer le fichier sur votre machine, compléter toutes les valeurs, puis le transférer sur le serveur (étape 2). Ce fichier ne doit **jamais** être commité dans Git.
 
---
 
### 2. Transférer les fichiers sur le serveur via Rsync
 
Depuis votre machine locale :
 
```bash
# ── Créer le répertoire de destination sur le serveur ──────────────────────
ssh user@IP_SERVEUR "mkdir -p /var/www/vite-et-gourmand"
 
# ── Transférer le projet proprement ────────────────────────────────────────
rsync -avz --delete \
  --exclude='vendor/' \
  --exclude='.git/' \
  --exclude='var/cache/' \
  --exclude='var/log/' \
  --exclude='.env*' \
  ./ user@IP_SERVEUR:/var/www/vite-et-gourmand/
 
# ── Transférer le fichier d'environnement de production ────────────────────
# On le renomme en .env.local sur le serveur :
# Symfony charge toujours .env.local en priorité, quel que soit APP_ENV.
scp .env.prod user@IP_SERVEUR:/var/www/vite-et-gourmand/.env.local
```

 
---
 
### 3. Se connecter au serveur
 
```bash
ssh user@IP_SERVEUR
cd /var/www/vite-et-gourmand
```
 
---
 
### 4. Vérifier le fichier `.env.local` sur le serveur
 
```bash
cat .env.local
# S'assurer que les variables critiques sont correctes :
# APP_ENV=prod
# APP_DEBUG=false
# APP_SECRET=<chaine_aleatoire>
# DATABASE_URL, MONGODB_URI, CORS_ALLOW_ORIGIN
```
 
---
 
### 5. Construire et démarrer les conteneurs
 
```bash
# Build et démarrage en arrière-plan
# Sans --profile dev : phpMyAdmin et Mongo Express ne sont PAS démarrés
docker compose up -d --build
```
 
---
 
### 6. Installer les dépendances PHP (mode production)
 
```bash
docker exec php-container composer install --no-dev --optimize-autoloader
# --no-dev              : exclut PHPUnit, Maker Bundle et les outils de dev
# --optimize-autoloader : génère un autoloader optimisé pour la performance
```
 
---
 
### 7. Exécuter les migrations de base de données
 
```bash
docker exec php-container php bin/console doctrine:migrations:migrate --no-interaction --env=prod
```
 
---
 
### 8. Vider et préchauffer le cache Symfony
 
```bash
docker exec php-container php bin/console cache:clear --env=prod
docker exec php-container php bin/console cache:warmup --env=prod
```
 
---
 
### 9. Vérifier l'état des conteneurs
 
```bash
docker compose ps
 
# Consulter les logs en temps réel en cas de problème
docker compose logs -f
docker compose logs php
docker compose logs nginx
```
 
---
 
### Mise à jour de l'application (redéploiement)
 
```bash
# 1. Depuis la machine locale, transférer les nouveaux fichiers
rsync -avz --delete \
  --exclude='vendor/' --exclude='.git/' --exclude='.env*' \
  ./vite-et-gourmand/ user@IP_SERVEUR:/var/www/vite-et-gourmand/
 
# 2. Sur le serveur : rebuild et redémarrage
ssh user@IP_SERVEUR
cd /var/www/vite-et-gourmand
docker compose up -d --build
 
# 3. Mettre à jour les dépendances, migrations et cache
docker exec php-container composer install --no-dev --optimize-autoloader
docker exec php-container php bin/console doctrine:migrations:migrate --no-interaction
docker exec php-container php bin/console cache:clear --env=prod
docker exec php-container php bin/console cache:warmup --env=prod
```
 
---
 
## Accès aux services
 
| Service | Développement | Production | Profil requis |
|---------|--------------|------------|---------------|
| Application web | `http://localhost:8080` | `https://viteetgourmand.ddns.net/` (ou IP) | — |En production réelle, le HTTPS devrait être configuré|
| API Symfony | `http://localhost:8080/api` | `https://viteetgourmand.ddns.net/api` | — |En production réelle, le HTTPS devrait être configuré|
| Documentation Swagger | http://localhost:8080/api/doc | ❌ désactivé en prod | — |
| phpMyAdmin | http://localhost:8081 | ❌ désactivé | `dev` |
| Mongo Express | http://localhost:8083 | ❌ désactivé | `dev` |
 
> ⚠️ phpMyAdmin et Mongo Express ne doivent **jamais** être exposés en production.
 
---
## Structure du projet
 
```
.
├── backend/                          # Application Symfony (PHP)
│   ├── src/
│   │   ├── Controller/               # Contrôleurs API REST
│   │   │   ├── Api/                  # Sous-dossier pour les endpoints API
│   │   │   ├── AdminController.php           # Gestion des statistiques et comptes employés
│   │   │   ├── AdresseController.php         # CRUD des adresses utilisateurs
│   │   │   ├── DishController.php            # Gestion des plats individuels
│   │   │   ├── EmployeeController.php        # Actions réservées aux employés
│   │   │   ├── MaterialController.php        # Gestion du matériel de restauration
│   │   │   ├── MaterialRentalController.php  # Gestion des locations de matériel
│   │   │   ├── MenuController.php            # CRUD des menus du catalogue
│   │   │   ├── NoticeController.php          # Gestion et modération des avis
│   │   │   ├── OrderController.php           # Création et suivi des commandes
│   │   │   ├── OrderItemController.php       # Gestion des lignes de commande
│   │   │   ├── RegimeController.php          # Gestion des régimes alimentaires
│   │   │   ├── RentalController.php          # Suivi des locations
│   │   │   ├── SecurityController.php        # Inscription, connexion, token API
│   │   │   └── ThemeMenuController.php       # Gestion des thèmes de menus (Noël, Pâques...)
│   │   │
│   │   ├── Entity/                   # Entités Doctrine (MySQL)
│   │   │   ├── Adresse.php           # Adresse postale d'un utilisateur
│   │   │   ├── Dish.php              # Plat composant un menu
│   │   │   ├── Material.php          # Matériel de restauration disponible à la location
│   │   │   ├── MaterialRental.php    # Location d'un matériel par un utilisateur
│   │   │   ├── Menu.php              # Menu proposé à la vente
│   │   │   ├── Notice.php            # Avis client sur une commande ou location
│   │   │   ├── Order.php             # Commande passée par un utilisateur
│   │   │   ├── OrderItem.php         # Ligne de commande (menu + quantité + prix unitaire)
│   │   │   ├── Regime.php            # Régime alimentaire (végétarien, sans gluten...)
│   │   │   ├── Rental.php            # Location de matériel
│   │   │   ├── ThemeMenu.php         # Thème événementiel d'un menu
│   │   │   └── User.php              # Compte utilisateur (roles, token API, infos perso)
│   │   │
│   │   ├── EventListener/            # Écouteurs d'événements Symfony
│   │   │   └── AccessDeniedListener.php  # Gestion personnalisée des erreurs 403 (accès refusé)
│   │   │
│   │   ├── Repository/                       # Repositories Doctrine (requêtes personnalisées)
│   │   │   ├── AdresseRepository.php         # Requêtes sur les adresses utilisateurs
│   │   │   ├── DishRepository.php            # Requêtes sur les plats
│   │   │   ├── MaterialRepository.php        # Requêtes sur le matériel de restauration
│   │   │   ├── MaterialRentalRepository.php  # Requêtes sur les locations de matériel
│   │   │   ├── MenuRepository.php            # Requêtes sur les menus du catalogue
│   │   │   ├── NoticeRepository.php          # Requêtes sur les avis clients
│   │   │   ├── OrderRepository.php           # Requêtes sur les commandes
│   │   │   ├── OrderItemRepository.php       # Requêtes sur les lignes de commande
│   │   │   ├── RegimeRepository.php          # Requêtes sur les régimes alimentaires
│   │   │   ├── RentalRepository.php          # Requêtes sur les locations
│   │   │   ├── ThemeMenuRepository.php       # Requêtes sur les thèmes de menus
│   │   │   └── UserRepository.php            # Requêtes sur les utilisateurs (auth, recherche)
│   │   └── Security/
│   │       └── ApiTokenAuthenticator.php  # Auth par header X-AUTH-TOKEN
│   │
│   ├── migrations/                   # Migrations Doctrine générées automatiquement
│   ├── config/                       # Configuration Symfony (security, services, routes)
│   └── composer.json                 # Dépendances PHP
│   ├── .env.local                        # Variables d'environnement locales (non versionné)
|   └── .env.prod                         # Variables de production (non versionné, déposé via SCP)
|
├── frontend/  
|   |__ js/                
|   │   |                               # Scripts JavaScript
│   │   ├── auth/
│   │   │   ├── signin.js                 # Logique de connexion
│   │   │   └── signup.js                 # Logique d'inscription
│   │   └── scripts.js                    # Scripts globaux partagés
│   ├── pages/                            # Pages HTML de l'application
│   │   ├── auth/
│   │   │   ├── signin.html               # Page de connexion
│   │   │   └── signup.html               # Page d'inscription
│   │   └── menu/
│   │       ├── detailmenu.html           # Page de détail d'un menu
│   │       └── ourmenus.html             # Page listing de tous les menus
│   │   ├── 404.html                      # Page d'erreur 404
│   │   ├── contact.html                  # Page formulaire de contact
│   │   ├── home.html                     # Page d'accueil
│   │   └── mentionslegales.html          # Page mentions légales
│   └── Router/                           # Routeur JavaScript côté client
│       ├── allRoutes.js                  # Déclaration de toutes les routes
│       ├── Route.js                      # Classe Route
│       └── router.js                     # Logique de navigation SPA
├── nginx/
│   └── default.conf                  # Configuration du serveur Nginx (routing PHP-FPM)
├── init-db.js                        # Script d'initialisation de la base MongoDB
├── Dockerfile                        # Image PHP 8.4-FPM avec extensions et Composer
├── docker-compose.yml                # Orchestration : MySQL, MongoDB, PHP, Nginx, outils dev

```
 
--- 

## Authentification API
 
L'API utilise un système d'authentification par **token** transmis dans l'en-tête HTTP `X-AUTH-TOKEN`.
 
Le token est stocké en base de données et associé à un compte utilisateur. À chaque requête, le `ApiTokenAuthenticator` vérifie sa présence et sa validité.
 
### Exemple de requête authentifiée
 
```http
GET /api/orders HTTP/1.1
Host: localhost:8080
X-AUTH-TOKEN: <votre_token>
```
 
En cas de token absent ou invalide, l'API retourne `401 Unauthorized` :
 
```json
{ "message": "Invalid API token." }
```
 
---
 
## Rôles et permissions
 
| Rôle | Héritage | Description |
|------|----------|-------------|
| `ROLE_USER` | — | Utilisateur inscrit, peut commander et gérer son espace |
| `ROLE_EMPLOYEE` | `ROLE_USER` | Employé : gère les commandes, menus et modère les avis |
| `ROLE_ADMIN` | `ROLE_EMPLOYEE` | Administrateur : accès aux stats et gestion des comptes |
 
---
 
## Dépendances
 
---
 
### 🐘 PHP
 
| Paquet | Version | Rôle |
|--------|---------|------|
| `php` | ≥ 8.4 | Langage principal du backend |
| `ext-ctype` | * | Extension PHP : vérification de types de caractères |
| `ext-iconv` | * | Extension PHP : conversion d'encodage de chaînes |
| `pdo_mysql` | — | Extension PHP : connexion PDO à MySQL (installée via docker) |
| `intl` | — | Extension PHP : internationalisation (dates, devises, locales) |
| `zip` | — | Extension PHP : manipulation d'archives ZIP |
| `opcache` | — | Extension PHP : cache bytecode pour accélérer l'exécution |
 
---
 
### 🎵 Symfony
 
| Paquet | Version | Rôle |
|--------|---------|------|
| `symfony/framework-bundle` | 8.0.* | Cœur du framework Symfony (kernel, container, routing) |
| `symfony/security-bundle` | 8.0.* | Gestion des rôles, authentification et autorisation |
| `symfony/serializer` | 8.0.* | Sérialisation / désérialisation des entités (JSON, XML) |
| `symfony/validator` | 8.0.* | Validation des données entrantes via annotations/attributs |
| `symfony/mailer` | 8.0.* | Envoi d'e-mails (formulaire de contact, confirmations) |
| `symfony/form` | 8.0.* | Construction et traitement de formulaires |
| `symfony/http-client` | 8.0.* | Client HTTP pour appels vers des API tierces |
| `symfony/translation` | 8.0.* | Internationalisation (i18n) et traduction de messages |
| `symfony/uid` | 8.0.* | Génération d'identifiants UUID |
| `symfony/dotenv` | 8.0.* | Chargement des variables d'environnement depuis `.env` |
| `symfony/runtime` | 8.0.* | Gestion du cycle de vie de l'application |
| `symfony/console` | 8.0.* | Commandes CLI (`bin/console`) |
| `symfony/process` | 8.0.* | Exécution de processus système depuis PHP |
| `symfony/string` | 8.0.* | Manipulation avancée de chaînes (slugify, unicode...) |
| `symfony/yaml` | 8.0.* | Parsing et génération de fichiers YAML |
| `symfony/expression-language` | 8.0.* | Évaluation d'expressions dynamiques (security voters, etc.) |
| `symfony/mime` | 8.0.* | Manipulation de types MIME (utilisé par Mailer) |
| `symfony/notifier` | 8.0.* | Envoi de notifications (e-mail, SMS, Slack...) |
| `symfony/web-link` | 8.0.* | Gestion des liens HTTP (preload, prefetch) |
| `symfony/intl` | 8.0.* | Données d'internationalisation (pays, langues, fuseaux) |
 
---
 
### 🗄️ Doctrine
 
| Paquet | Version | Rôle |
|--------|---------|------|
| `doctrine/orm` | ^3.6 | ORM principal : mapping objet-relationnel vers MySQL |
| `doctrine/doctrine-bundle` | ^3.2 | Intégration de Doctrine dans Symfony |
| `doctrine/doctrine-migrations-bundle` | ^4.0 | Gestion des migrations de schéma de base de données |
 
---
 
### 🌐 API & Documentation
 
| Paquet | Version | Rôle |
|--------|---------|------|
| `nelmio/api-doc-bundle` | ^5.9 | Génération automatique de la documentation Swagger/OpenAPI |
| `nelmio/cors-bundle` | ^2.6 | Gestion des headers CORS pour les appels cross-origin |
 
---
 
### 🖼️ Twig & Frontend
 
| Paquet | Version | Rôle |
|--------|---------|------|
| `twig/twig` | ^3.0 | Moteur de templates PHP |
| `twig/extra-bundle` | ^3.0 | Extensions Twig supplémentaires (markdown, intl...) |
| `symfony/twig-bundle` | 8.0.* | Intégration Twig dans Symfony |
| `symfony/asset` | 8.0.* | Gestion des assets statiques (CSS, JS, images) |
| `symfony/asset-mapper` | 8.0.* | Gestion moderne des imports JavaScript (sans bundler) |
| `symfony/stimulus-bundle` | ^2.32 | Intégration du framework JavaScript Stimulus |
| `symfony/ux-turbo` | ^2.32 | Navigation SPA-like sans rechargement de page (Turbo Drive) |
 
---
 
### 🛠️ Développement & Qualité
 
| Paquet | Version | Rôle |
|--------|---------|------|
| `phpunit/phpunit` | ^12.5 | Framework de tests unitaires et fonctionnels |
| `symfony/maker-bundle` | ^1.0 | Génération de code (entités, contrôleurs, migrations...) |
| `symfony/debug-bundle` | 8.0.* | Outils de debug (dump, var_dumper) |
| `symfony/web-profiler-bundle` | 8.0.* | Barre de debug Symfony (profiler) — dev uniquement |
| `symfony/stopwatch` | 8.0.* | Mesure des performances (utilisé par le profiler) |
| `symfony/browser-kit` | 8.0.* | Simulation de navigateur pour les tests fonctionnels |
| `symfony/css-selector` | 8.0.* | Sélecteurs CSS dans les tests (companion de BrowserKit) |
| `phpstan/phpdoc-parser` | ^2.3 | Analyse statique des annotations PHPDoc |
| `phpdocumentor/reflection-docblock` | ^6.0 | Lecture des blocs de documentation PHP (utilisé par Serializer) |
 
---
 
### 🐳 Docker — Infrastructure
 
| Image | Version | Rôle |
|-------|---------|------|
| `php:8.4-fpm` | 8.4 | Serveur PHP FastCGI Process Manager, base du conteneur backend |
| `mysql:8.0` | 8.0 | Base de données relationnelle principale |
| `mongo:7.0` | 7.0 | Base de données NoSQL pour les données non structurées |
| `nginx:1.27-alpine` | 1.27 | Serveur web, reverse proxy et routage vers PHP-FPM |
| `phpmyadmin:5.2` | 5.2 | Interface graphique MySQL — dev uniquement |
| `mongo-express:latest` | latest | Interface graphique MongoDB — dev uniquement |
| `composer:2` | 2.x | Gestionnaire de dépendances PHP (image multi-stage) |
| `mlocati/php-extension-installer` | latest | Installateur simplifié d'extensions PHP dans Docker |
 

 
