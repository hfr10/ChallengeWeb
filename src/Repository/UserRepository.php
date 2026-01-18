<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\User;

/**
 * Repository pour les utilisateurs
 */
class UserRepository
{
    /**
     * Trouve un utilisateur par son ID
     */
    public function findById(int $id): ?User
    {
        $data = Database::fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
        return $data ? User::fromArray($data) : null;
    }

    /**
     * Trouve un utilisateur par son email
     */
    public function findByEmail(string $email): ?User
    {
        $data = Database::fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
        return $data ? User::fromArray($data) : null;
    }

    /**
     * Récupère tous les utilisateurs
     */
    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $data = Database::fetchAll(
            "SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        return array_map(fn($row) => User::fromArray($row), $data);
    }

    /**
     * Récupère les utilisateurs par rôle
     */
    public function findByRole(string $role): array
    {
        $data = Database::fetchAll(
            "SELECT * FROM users WHERE role = ? ORDER BY created_at DESC",
            [$role]
        );
        return array_map(fn($row) => User::fromArray($row), $data);
    }

    /**
     * Compte le nombre d'utilisateurs
     */
    public function count(): int
    {
        $result = Database::fetchOne("SELECT COUNT(*) as count FROM users");
        return (int) $result['count'];
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function create(User $user): int
    {
        $data = [
            'email' => $user->email,
            'password' => $user->password,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'address' => $user->address,
            'city' => $user->city,
            'postal_code' => $user->postal_code,
            'role' => $user->role,
        ];

        return Database::insert('users', $data);
    }

    /**
     * Met à jour un utilisateur
     */
    public function update(User $user): bool
    {
        $data = [
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'address' => $user->address,
            'city' => $user->city,
            'postal_code' => $user->postal_code,
            'role' => $user->role,
        ];

        return Database::update('users', $data, 'id = ?', [$user->id]) > 0;
    }

    /**
     * Met à jour le mot de passe
     */
    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        return Database::update('users', ['password' => $hashedPassword], 'id = ?', [$userId]) > 0;
    }

    /**
     * Supprime un utilisateur
     */
    public function delete(int $id): bool
    {
        return Database::delete('users', 'id = ?', [$id]) > 0;
    }

    /**
     * Vérifie si un email existe
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = Database::fetchOne($sql, $params);
        return (int) $result['count'] > 0;
    }
}
