<?php

/**
 * Script de migration de base de données
 * Exécute les fichiers SQL dans database/migrations/
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
if (file_exists(dirname(__DIR__) . '/.env')) {
    $dotenv->load();
}

echo "=== Migration de la base de données ===\n\n";

// Configuration de la base de données
$config = require __DIR__ . '/../config/database.php';

try {
    // Connexion à PostgreSQL (sans la base de données pour pouvoir la créer)
    $dsnWithoutDb = sprintf(
        '%s:host=%s;port=%s',
        $config['driver'],
        $config['host'],
        $config['port']
    );

    $pdo = new PDO($dsnWithoutDb, $config['username'], $config['password'], $config['options']);

    // Vérifier si la base de données existe
    $dbName = $config['database'];
    $stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '{$dbName}'");

    if ($stmt->fetchColumn() === false) {
        echo "Création de la base de données '{$dbName}'...\n";
        $pdo->exec("CREATE DATABASE {$dbName}");
        echo "Base de données créée avec succès.\n\n";
    } else {
        echo "La base de données '{$dbName}' existe déjà.\n\n";
    }

    // Se reconnecter à la base de données
    $dsn = sprintf(
        '%s:host=%s;port=%s;dbname=%s',
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['database']
    );

    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);

    // Exécuter les migrations
    $migrationsPath = __DIR__ . '/migrations';
    $files = glob($migrationsPath . '/*.sql');
    sort($files);

    foreach ($files as $file) {
        $filename = basename($file);
        echo "Exécution de {$filename}...\n";

        $sql = file_get_contents($file);
        $pdo->exec($sql);

        echo "  ✓ Migration {$filename} exécutée avec succès.\n";
    }

    echo "\n=== Migrations terminées avec succès ===\n";

} catch (PDOException $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    exit(1);
}
