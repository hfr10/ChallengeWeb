# Guide d'installation - Football Shop

## Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **PHP 8.1+** avec les extensions suivantes :
  - pdo
  - pdo_pgsql
  - mbstring
  - json
- **PostgreSQL 14+**
- **Composer 2.x**
- **Node.js 18+** et npm (pour le développement frontend)

### Vérifier les versions

```bash
php -v           # PHP 8.1 ou supérieur
composer -V      # Composer 2.x
psql --version   # PostgreSQL 14+
node -v          # Node.js 18+
```

## Installation

### 1. Cloner le projet

```bash
git clone <url-du-repo>
cd ChallengeWeb
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

Copier le fichier d'exemple et le configurer :

```bash
cp .env.example .env
```

Éditer `.env` avec vos paramètres :

```env
# Configuration de l'application
APP_NAME="Football Shop"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:8000

# Configuration PostgreSQL
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=football_shop
DB_USERNAME=postgres
DB_PASSWORD=votre_mot_de_passe

# Configuration de session
SESSION_LIFETIME=120
```

### 4. Créer la base de données

#### Option A : Via le script de migration

```bash
php database/migrate.php
```

Ce script va :
- Créer la base de données si elle n'existe pas
- Exécuter toutes les migrations SQL

#### Option B : Manuellement

```bash
# Se connecter à PostgreSQL
psql -U postgres

# Créer la base de données
CREATE DATABASE football_shop;

# Se déconnecter
\q

# Exécuter les migrations
psql -U postgres -d football_shop -f database/migrations/001_create_tables.sql
```

### 5. Charger les données de test (optionnel)

```bash
php database/seed.php
```

Cela créera :
- Un compte administrateur : `admin@footballshop.fr` / `admin123`
- 5 clients de test (mot de passe : `customer123`)
- 5 catégories de produits
- 12 produits avec tailles

### 6. Lancer le serveur de développement

```bash
# Utiliser le serveur PHP intégré
php -S localhost:8000 -t public

# OU via Composer
composer start
```

Le site est accessible sur : **http://localhost:8000**

## Structure des dossiers

```
ChallengeWeb/
├── config/             # Fichiers de configuration
├── database/           # Migrations et seeds
├── docs/               # Documentation
├── public/             # Point d'entrée web (index.php)
├── src/                # Code source PHP
│   ├── Controllers/    # Contrôleurs MVC
│   ├── Core/           # Classes du framework
│   ├── Middleware/     # Middlewares
│   ├── Models/         # Modèles/Entités
│   ├── Repository/     # Accès aux données
│   └── Services/       # Services métier
├── vendor/             # Dépendances Composer
├── views/              # Templates PHP
├── .env                # Variables d'environnement
└── composer.json       # Configuration Composer
```

## Comptes de test

Après avoir exécuté `php database/seed.php` :

| Type | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@footballshop.fr | admin123 |
| Client | (emails générés) | customer123 |

## Dépannage

### Erreur de connexion à PostgreSQL

1. Vérifiez que PostgreSQL est démarré
2. Vérifiez les identifiants dans `.env`
3. Vérifiez que l'extension `pdo_pgsql` est activée :
   ```bash
   php -m | grep pdo_pgsql
   ```

### Page blanche ou erreur 500

1. Activez l'affichage des erreurs dans `public/index.php`
2. Vérifiez les logs PHP
3. Assurez-vous que le fichier `.env` existe et est configuré

### Problèmes d'autoloading

```bash
composer dump-autoload -o
```

## Commandes utiles

```bash
# Installer les dépendances
composer install

# Lancer le serveur
composer start

# Exécuter les migrations
composer migrate

# Charger les données de test
composer seed
```
