<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\Category;

/**
 * Repository pour les catégories
 */
class CategoryRepository
{
    /**
     * Trouve une catégorie par son ID
     */
    public function findById(int $id): ?Category
    {
        $data = Database::fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);
        return $data ? Category::fromArray($data) : null;
    }

    /**
     * Trouve une catégorie par son slug
     */
    public function findBySlug(string $slug): ?Category
    {
        $data = Database::fetchOne("SELECT * FROM categories WHERE slug = ?", [$slug]);
        return $data ? Category::fromArray($data) : null;
    }

    /**
     * Récupère toutes les catégories actives
     */
    public function findAll(bool $activeOnly = true): array
    {
        $sql = "SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = true) as product_count
                FROM categories c";

        if ($activeOnly) {
            $sql .= " WHERE c.is_active = true";
        }

        $sql .= " ORDER BY c.sort_order, c.name";

        $data = Database::fetchAll($sql);

        return array_map(function ($row) {
            $category = Category::fromArray($row);
            $category->product_count = (int) $row['product_count'];
            return $category;
        }, $data);
    }

    /**
     * Récupère les catégories principales (sans parent)
     */
    public function findRootCategories(bool $activeOnly = true): array
    {
        $sql = "SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = true) as product_count
                FROM categories c
                WHERE c.parent_id IS NULL";

        if ($activeOnly) {
            $sql .= " AND c.is_active = true";
        }

        $sql .= " ORDER BY c.sort_order, c.name";

        $data = Database::fetchAll($sql);

        return array_map(function ($row) {
            $category = Category::fromArray($row);
            $category->product_count = (int) $row['product_count'];
            return $category;
        }, $data);
    }

    /**
     * Crée une catégorie
     */
    public function create(Category $category): int
    {
        $data = [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'image' => $category->image,
            'parent_id' => $category->parent_id,
            'sort_order' => $category->sort_order,
            'is_active' => $category->is_active ? 'true' : 'false',
        ];

        return Database::insert('categories', $data);
    }

    /**
     * Met à jour une catégorie
     */
    public function update(Category $category): bool
    {
        $data = [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'image' => $category->image,
            'parent_id' => $category->parent_id,
            'sort_order' => $category->sort_order,
            'is_active' => $category->is_active ? 'true' : 'false',
        ];

        return Database::update('categories', $data, 'id = ?', [$category->id]) > 0;
    }

    /**
     * Supprime une catégorie
     */
    public function delete(int $id): bool
    {
        // Les produits seront mis à null grâce à ON DELETE SET NULL
        return Database::delete('categories', 'id = ?', [$id]) > 0;
    }

    /**
     * Vérifie si un slug existe
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = Database::fetchOne($sql, $params);
        return (int) $result['count'] > 0;
    }
}
