<?php

namespace App\Models;

/**
 * Modèle Category
 * Représente une catégorie de produits
 */
class Category
{
    public ?int $id = null;
    public string $name;
    public string $slug;
    public ?string $description = null;
    public ?string $image = null;
    public ?int $parent_id = null;
    public int $sort_order = 0;
    public bool $is_active = true;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    // Relations
    public ?Category $parent = null;
    public array $children = [];
    public int $product_count = 0;

    /**
     * Crée une instance à partir d'un tableau
     */
    public static function fromArray(array $data): self
    {
        $category = new self();
        foreach ($data as $key => $value) {
            if (property_exists($category, $key)) {
                if ($key === 'is_active') {
                    $category->$key = (bool) $value;
                } else {
                    $category->$key = $value;
                }
            }
        }
        return $category;
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
            'image' => $this->image,
            'parent_id' => $this->parent_id,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
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
}
