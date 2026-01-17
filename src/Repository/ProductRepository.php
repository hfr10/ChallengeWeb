<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\Product;
use App\Models\Category;

/**
 * Repository pour les produits
 * Gère les opérations CRUD et requêtes sur les produits
 */
class ProductRepository
{
    /**
     * Trouve un produit par son ID
     */
    public function findById(int $id, bool $withRelations = true): ?Product
    {
        $data = Database::fetchOne("SELECT * FROM products WHERE id = ?", [$id]);

        if (!$data) {
            return null;
        }

        $product = Product::fromArray($data);

        if ($withRelations) {
            $this->loadRelations($product);
        }

        return $product;
    }

    /**
     * Trouve un produit par son slug
     */
    public function findBySlug(string $slug, bool $withRelations = true): ?Product
    {
        $data = Database::fetchOne("SELECT * FROM products WHERE slug = ?", [$slug]);

        if (!$data) {
            return null;
        }

        $product = Product::fromArray($data);

        if ($withRelations) {
            $this->loadRelations($product);
        }

        return $product;
    }

    /**
     * Charge les relations d'un produit
     */
    private function loadRelations(Product $product): void
    {
        // Charger la catégorie
        if ($product->category_id) {
            $categoryData = Database::fetchOne(
                "SELECT * FROM categories WHERE id = ?",
                [$product->category_id]
            );
            if ($categoryData) {
                $product->category = Category::fromArray($categoryData);
            }
        }

        // Charger les tailles
        $sizes = Database::fetchAll(
            "SELECT * FROM product_sizes WHERE product_id = ? ORDER BY size",
            [$product->id]
        );
        $product->sizes = $sizes;
    }

    /**
     * Récupère les produits avec filtres et pagination
     */
    public function findAll(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1";
        $params = [];

        // Filtre actif
        if (!isset($filters['include_inactive']) || !$filters['include_inactive']) {
            $sql .= " AND p.is_active = true";
        }

        // Filtre catégorie
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        // Filtre catégorie par slug
        if (!empty($filters['category_slug'])) {
            $sql .= " AND c.slug = ?";
            $params[] = $filters['category_slug'];
        }

        // Filtre featured
        if (!empty($filters['featured'])) {
            $sql .= " AND p.is_featured = true";
        }

        // Filtre prix min
        if (!empty($filters['price_min'])) {
            $sql .= " AND COALESCE(p.sale_price, p.price) >= ?";
            $params[] = $filters['price_min'];
        }

        // Filtre prix max
        if (!empty($filters['price_max'])) {
            $sql .= " AND COALESCE(p.sale_price, p.price) <= ?";
            $params[] = $filters['price_max'];
        }

        // Filtre marque
        if (!empty($filters['brand'])) {
            $sql .= " AND p.brand = ?";
            $params[] = $filters['brand'];
        }

        // Recherche
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name ILIKE ? OR p.description ILIKE ? OR p.brand ILIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Tri
        $orderBy = match ($filters['sort'] ?? 'newest') {
            'price_asc' => 'COALESCE(p.sale_price, p.price) ASC',
            'price_desc' => 'COALESCE(p.sale_price, p.price) DESC',
            'name' => 'p.name ASC',
            default => 'p.created_at DESC',
        };
        $sql .= " ORDER BY {$orderBy}";

        // Pagination
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $data = Database::fetchAll($sql, $params);

        return array_map(function ($row) {
            $product = Product::fromArray($row);
            if (isset($row['category_name'])) {
                $product->category = new Category();
                $product->category->name = $row['category_name'];
                $product->category->slug = $row['category_slug'];
            }
            return $product;
        }, $data);
    }

    /**
     * Compte les produits selon les filtres
     */
    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1";
        $params = [];

        if (!isset($filters['include_inactive']) || !$filters['include_inactive']) {
            $sql .= " AND p.is_active = true";
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['category_slug'])) {
            $sql .= " AND c.slug = ?";
            $params[] = $filters['category_slug'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name ILIKE ? OR p.description ILIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $result = Database::fetchOne($sql, $params);
        return (int) $result['count'];
    }

    /**
     * Récupère les produits vedettes
     */
    public function findFeatured(int $limit = 8): array
    {
        return $this->findAll(['featured' => true], $limit);
    }

    /**
     * Récupère les marques disponibles
     */
    public function getBrands(): array
    {
        $data = Database::fetchAll(
            "SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND is_active = true ORDER BY brand"
        );
        return array_column($data, 'brand');
    }

    /**
     * Crée un produit
     */
    public function create(Product $product): int
    {
        $data = [
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'price' => $product->price,
            'sale_price' => $product->sale_price,
            'stock' => $product->stock,
            'sku' => $product->sku,
            'image' => $product->image,
            'images' => $product->images,
            'category_id' => $product->category_id,
            'brand' => $product->brand,
            'is_active' => $product->is_active ? 'true' : 'false',
            'is_featured' => $product->is_featured ? 'true' : 'false',
        ];

        return Database::insert('products', $data);
    }

    /**
     * Met à jour un produit
     */
    public function update(Product $product): bool
    {
        $data = [
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'price' => $product->price,
            'sale_price' => $product->sale_price,
            'stock' => $product->stock,
            'sku' => $product->sku,
            'image' => $product->image,
            'images' => $product->images,
            'category_id' => $product->category_id,
            'brand' => $product->brand,
            'is_active' => $product->is_active ? 'true' : 'false',
            'is_featured' => $product->is_featured ? 'true' : 'false',
        ];

        return Database::update('products', $data, 'id = ?', [$product->id]) > 0;
    }

    /**
     * Supprime un produit
     */
    public function delete(int $id): bool
    {
        return Database::delete('products', 'id = ?', [$id]) > 0;
    }

    /**
     * Met à jour les tailles d'un produit
     */
    public function updateSizes(int $productId, array $sizes): void
    {
        // Supprimer les anciennes tailles
        Database::delete('product_sizes', 'product_id = ?', [$productId]);

        // Ajouter les nouvelles
        foreach ($sizes as $size) {
            Database::insert('product_sizes', [
                'product_id' => $productId,
                'size' => $size['size'],
                'stock' => $size['stock'] ?? 0,
                'price_adjustment' => $size['price_adjustment'] ?? 0,
            ]);
        }
    }

    /**
     * Vérifie si un slug existe
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM products WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = Database::fetchOne($sql, $params);
        return (int) $result['count'] > 0;
    }
}
