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
     * Affiche les produits vedettes et catégories
     */
    public function index(): void
    {
        // Récupération des produits vedettes (8 maximum)
        $featuredProducts = $this->productRepository->findFeatured(8);

        // Récupération des catégories racines
        $categories = $this->categoryRepository->findRootCategories();

        // Récupération des derniers produits ajoutés
        $latestProducts = $this->productRepository->findAll([], 4);

        $this->render('home/index', [
            'title' => 'Accueil - Football Shop',
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'latestProducts' => $latestProducts,
        ]);
    }
}
