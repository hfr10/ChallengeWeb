<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Gestionnaire de connexion à la base de données PostgreSQL
 * Implémente le pattern Singleton pour une instance unique de PDO
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Empêcher l'instanciation directe
     */
    private function __construct()
    {
    }

    /**
     * Empêcher le clonage
     */
    private function __clone()
    {
    }

    /**
     * Retourne l'instance PDO (singleton)
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }
        return self::$instance;
    }

    /**
     * Crée la connexion PDO
     */
    private static function createConnection(): PDO
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database']
        );

        try {
            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );

            // Définir le jeu de caractères
            $pdo->exec("SET NAMES '{$config['charset']}'");

            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException(
                "Erreur de connexion à la base de données: " . $e->getMessage(),
                (int) $e->getCode()
            );
        }
    }

    /**
     * Exécute une requête préparée
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Récupère une seule ligne
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Récupère toutes les lignes
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Insère une ligne et retourne l'ID
     */
    public static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        self::query($sql, array_values($data));

        return (int) self::getInstance()->lastInsertId();
    }

    /**
     * Met à jour des lignes
     */
    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';

        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        $stmt = self::query($sql, [...array_values($data), ...$whereParams]);

        return $stmt->rowCount();
    }

    /**
     * Supprime des lignes
     */
    public static function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = self::query($sql, $params);

        return $stmt->rowCount();
    }

    /**
     * Démarre une transaction
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * Valide une transaction
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * Annule une transaction
     */
    public static function rollback(): bool
    {
        return self::getInstance()->rollBack();
    }
}
