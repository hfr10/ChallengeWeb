<?php

namespace App\Models;

/**
 * Modèle User
 * Représente un utilisateur du système
 */
class User
{
    public ?int $id = null;
    public string $email;
    public string $password;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $phone = null;
    public ?string $address = null;
    public ?string $city = null;
    public ?string $postal_code = null;
    public string $role = 'customer';
    public ?string $created_at = null;
    public ?string $updated_at = null;

    /**
     * Crée une instance à partir d'un tableau
     */
    public static function fromArray(array $data): self
    {
        $user = new self();
        foreach ($data as $key => $value) {
            if (property_exists($user, $key)) {
                $user->$key = $value;
            }
        }
        return $user;
    }

    /**
     * Convertit en tableau
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Retourne le nom complet
     */
    public function getFullName(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    /**
     * Vérifie si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie le mot de passe
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * Hash le mot de passe avant sauvegarde
     */
    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
}
