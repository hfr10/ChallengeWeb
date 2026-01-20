<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;

/**
 * Contrôleur des produits
 */
class ProductController extends Controller
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * Liste des produits avec filtres et pagination
     */
    public function index(): void
    {
        $query = $this->getQueryData();
        $page = max(1, (int) ($query['page'] ?? 1));
        $perPage = 12; // Nombre de produits par page

        // Récupération des filtres depuis la requête
        $filters = [
            'search' => $query['search'] ?? null,
            'category_id' => $query['category'] ?? null,
            'brand' => $query['brand'] ?? null,
            'price_min' => $query['price_min'] ?? null,
            'price_max' => $query['price_max'] ?? null,
            'sort' => $query['sort'] ?? 'newest',
        ];

        // Récupération des produits avec filtres
            $filters,
            $perPage,
            ($page - 1) * $perPage
        );

        $totalProducts = $this->productRepository->count($filters);
        $totalPages = ceil($totalProducts / $perPage);

        $categories = $this->categoryRepository->findAll();
        $brands = $this->productRepository->getBrands();

        $this->render('products/index', [
            'title' => 'Tous les produits',
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'perPage' => $perPage,
                'totalItems' => $totalProducts,
            ],
        ]);
    }

    /**
     * Détail d'un produit
     */
    public function show(string $slug): void
    {
        $product = $this->productRepository->findBySlug($slug);

        if (!$product || !$product->is_active) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Produit non trouvé']);
            return;
        }

        // Produits similaires (même catégorie)
        $relatedProducts = [];
        if ($product->category_id) {
            $relatedProducts = $this->productRepository->findAll(
                ['category_id' => $product->category_id],
                4
            );
            // Exclure le produit actuel
            $relatedProducts = array_filter(
                $relatedProducts,
                fn($p) => $p->id !== $product->id
            );
        }

        $this->render('products/show', [
            'title' => $product->name,
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * Produits par catégorie
     */
    public function byCategory(string $slug): void
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if (!$category || !$category->is_active) {
            http_response_code(404);
            $this->render('errors/404', ['title' => 'Catégorie non trouvée']);
            return;
        }

        $query = $this->getQueryData();
        $page = max(1, (int) ($query['page'] ?? 1));
        $perPage = 12;

        $filters = [
            'category_slug' => $slug,
            'brand' => $query['brand'] ?? null,
            'price_min' => $query['price_min'] ?? null,
            'price_max' => $query['price_max'] ?? null,
            'sort' => $query['sort'] ?? 'newest',
        ];

        $products = $this->productRepository->findAll(
            $filters,
            $perPage,
            ($page - 1) * $perPage
        );

        $totalProducts = $this->productRepository->count($filters);
        $totalPages = ceil($totalProducts / $perPage);

        $categories = $this->categoryRepository->findAll();
        $brands = $this->productRepository->getBrands();

        $this->render('products/index', [
            'title' => $category->name,
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => $totalPages,
                'perPage' => $perPage,
                'totalItems' => $totalProducts,
            ],
        ]);
    }
}
