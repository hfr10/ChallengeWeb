<?php

namespace App\Core;

/**
 * Validateur de données
 * Fournit des méthodes pour valider les entrées utilisateur
 * Supporte validation requise, email, longueur, comparaison
 */
class Validator
{
    private array $errors = [];
    private array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Valide que le champ est requis
     */
    public function required(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        if ($value === null || $value === '') {
            $this->addError($field, $message ?? "Le champ {$field} est requis.");
        }
        return $this;
    }

    /**
     * Valide que le champ est un email valide
     */
    public function email(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message ?? "L'adresse email n'est pas valide.");
        }
        return $this;
    }

    /**
     * Valide la longueur minimale
     */
    public function minLength(string $field, int $min, string $message = null): self
    {
        $value = $this->getValue($field);
        if ($value && strlen($value) < $min) {
            $this->addError($field, $message ?? "Le champ {$field} doit contenir au moins {$min} caractères.");
        }
        return $this;
    }

    /**
     * Valide la longueur maximale
     */
    public function maxLength(string $field, int $max, string $message = null): self
    {
        $value = $this->getValue($field);
        if ($value && strlen($value) > $max) {
            $this->addError($field, $message ?? "Le champ {$field} ne doit pas dépasser {$max} caractères.");
        }
        return $this;
    }

    /**
     * Valide que deux champs sont identiques
     */
    public function matches(string $field, string $otherField, string $message = null): self
    {
        $value = $this->getValue($field);
        $otherValue = $this->getValue($otherField);
        if ($value !== $otherValue) {
            $this->addError($field, $message ?? "Les champs {$field} et {$otherField} doivent être identiques.");
        }
        return $this;
    }

    /**
     * Valide que le champ est numérique
     */
    public function numeric(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        if ($value && !is_numeric($value)) {
            $this->addError($field, $message ?? "Le champ {$field} doit être un nombre.");
        }
        return $this;
    }

    /**
     * Valide une valeur minimale (nombre)
     */
    public function min(string $field, float $min, string $message = null): self
    {
        $value = $this->getValue($field);
        if ($value !== null && is_numeric($value) && (float) $value < $min) {
            $this->addError($field, $message ?? "Le champ {$field} doit être au moins {$min}.");
        }
        return $this;
    }

    /**
     * Valide une valeur maximale (nombre)
     */
    public function max(string $field, float $max, string $message = null): self
    {
        $value = $this->getValue($field);
        if ($value !== null && is_numeric($value) && (float) $value > $max) {
            $this->addError($field, $message ?? "Le champ {$field} ne doit pas dépasser {$max}.");
        }
        return $this;
    }

    /**
     * Valide contre une expression régulière
     */
    public function regex(string $field, string $pattern, string $message = null): self
    {
        $value = $this->getValue($field);
        if ($value && !preg_match($pattern, $value)) {
            $this->addError($field, $message ?? "Le format du champ {$field} est invalide.");
        }
        return $this;
    }

    /**
     * Valide que le champ est dans une liste de valeurs
     */
    public function in(string $field, array $values, string $message = null): self
    {
        $value = $this->getValue($field);
        if ($value && !in_array($value, $values, true)) {
            $this->addError($field, $message ?? "La valeur du champ {$field} n'est pas valide.");
        }
        return $this;
    }

    /**
     * Valide que le champ est un entier positif
     */
    public function positiveInteger(string $field, string $message = null): self
    {
        $value = $this->getValue($field);
        if ($value !== null && (!is_numeric($value) || (int) $value <= 0 || (int) $value != $value)) {
            $this->addError($field, $message ?? "Le champ {$field} doit être un entier positif.");
        }
        return $this;
    }

    /**
     * Récupère la valeur d'un champ
     */
    private function getValue(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }

    /**
     * Ajoute une erreur
     */
    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Vérifie si la validation a réussi
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Retourne les erreurs
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne la première erreur d'un champ
     */
    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Retourne toutes les premières erreurs (une par champ)
     */
    public function getFirstErrors(): array
    {
        $firstErrors = [];
        foreach ($this->errors as $field => $errors) {
            $firstErrors[$field] = $errors[0];
        }
        return $firstErrors;
    }
}
