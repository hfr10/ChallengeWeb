<?php

namespace App\Models;

/**
 * Modèle Product
 * Représente un produit du catalogue
 */
class Product
{
    public ?int $id = null;
    public string $name;
    public string $slug;
    public ?string $description = null;
    public ?string $short_description = null;
    public float $price;
    public ?float $sale_price = null;
    public int $stock = 0;
    public ?string $sku = null;
    public ?string $image = null;
    public ?string $images = null;
    public ?int $category_id = null;
    public ?string $brand = null;
    public bool $is_active = true;
    public bool $is_featured = false;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    // Relations
    public ?Category $category = null;
    public array $sizes = [];

    /**
     * Crée une instance à partir d'un tableau
     */
    public static function fromArray(array $data): self
    {
        $product = new self();
        foreach ($data as $key => $value) {
            if (property_exists($product, $key)) {
                if (in_array($key, ['is_active', 'is_featured'])) {
                    $product->$key = (bool) $value;
                } elseif (in_array($key, ['price', 'sale_price'])) {
                    $product->$key = $value !== null ? (float) $value : null;
                } elseif ($key === 'stock') {
                    $product->$key = (int) $value;
                } else {
                    $product->$key = $value;
                }
            }
        }
        return $product;
    }

    /**
     * Convertit en tableau
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'image' => $this->image,
            'images' => $this->images,
            'category_id' => $this->category_id,
            'brand' => $this->brand,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Retourne le prix effectif (avec promotion si applicable)
     */
    public function getEffectivePrice(): float
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Vérifie si le produit est en promotion
     */
    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    /**
     * Calcule le pourcentage de réduction
     */
    public function getDiscountPercentage(): int
    {
        if (!$this->isOnSale()) {
            return 0;
        }
        return (int) round((1 - $this->sale_price / $this->price) * 100);
    }

    /**
     * Vérifie si le produit est en stock
     */
    public function isInStock(): bool
    {
        // Si des tailles sont définies, vérifier leur stock
        if (!empty($this->sizes)) {
            foreach ($this->sizes as $size) {
                if ($size['stock'] > 0) {
                    return true;
                }
            }
            return false;
        }
        return $this->stock > 0;
    }

    /**
     * Génère un slug à partir du nom
     */
    public static function generateSlug(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }

    /**
     * Retourne l'URL de l'image ou un placeholder
     */
    public function getImageUrl(): string
    {
        if ($this->image) {
            return '/images/products/' . $this->image;
        }
        return 'https://via.placeholder.com/400x400?text=' . urlencode($this->name);
    }
}
