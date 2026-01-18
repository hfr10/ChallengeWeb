<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;

/**
 * Contrôleur de la page d'accueil
 */
class HomeController extends Controller
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * Page d'accueil
     */
    public function index(): void
    {
        // Produits vedettes
        $featuredProducts = $this->productRepository->findFeatured(8);

        // Catégories principales
        $categories = $this->categoryRepository->findRootCategories();

        // Derniers produits
        $latestProducts = $this->productRepository->findAll([], 4);

        $this->render('home/index', [
            'title' => 'Accueil - Football Shop',
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'latestProducts' => $latestProducts,
        ]);
    }
}
