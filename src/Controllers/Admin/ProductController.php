<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Models\Product;

/**
 * Contrôleur admin pour les produits
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
     * Liste des produits
     */
    public function index(): void
    {
        $query = $this->getQueryData();
        $page = max(1, (int) ($query['page'] ?? 1));
        $perPage = 20;

        $filters = [
            'include_inactive' => true,
            'search' => $query['search'] ?? null,
            'category_id' => $query['category'] ?? null,
        ];

        $products = $this->productRepository->findAll($filters, $perPage, ($page - 1) * $perPage);
        $totalProducts = $this->productRepository->count($filters);
        $categories = $this->categoryRepository->findAll(false);

        $this->render('admin/products/index', [
            'title' => 'Produits',
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => ceil($totalProducts / $perPage),
                'totalItems' => $totalProducts,
            ],
        ], 'admin');
    }

    /**
     * Formulaire de création
     */
    public function create(): void
    {
        $categories = $this->categoryRepository->findAll(false);

        $this->render('admin/products/form', [
            'title' => 'Nouveau produit',
            'product' => null,
            'categories' => $categories,
        ], 'admin');
    }

    /**
     * Enregistre un nouveau produit
     */
    public function store(): void
    {
        $data = $this->getPostData();

        $validator = new Validator($data);
        $validator
            ->required('name', 'Le nom est requis.')
            ->required('price', 'Le prix est requis.')
            ->numeric('price');

        if (!$validator->isValid()) {
            Session::flash('errors', $validator->getFirstErrors());
            Session::flash('old', $data);
            $this->redirect('/admin/products/create');
            return;
        }

        $product = new Product();
        $this->fillProduct($product, $data);

        // Générer le slug
        $product->slug = Product::generateSlug($product->name);
        $counter = 1;
        while ($this->productRepository->slugExists($product->slug)) {
            $product->slug = Product::generateSlug($product->name) . '-' . $counter;
            $counter++;
        }

        $productId = $this->productRepository->create($product);

        // Gérer les tailles
        if (!empty($data['sizes'])) {
            $this->updateSizes($productId, $data['sizes']);
        }

        Session::flash('success', 'Produit créé avec succès.');
        $this->redirect('/admin/products');
    }

    /**
     * Formulaire d'édition
     */
    public function edit(string $id): void
    {
        $product = $this->productRepository->findById((int) $id);

        if (!$product) {
            $this->redirectWithMessage('/admin/products', 'error', 'Produit non trouvé.');
            return;
        }

        $categories = $this->categoryRepository->findAll(false);

        $this->render('admin/products/form', [
            'title' => 'Modifier ' . $product->name,
            'product' => $product,
            'categories' => $categories,
        ], 'admin');
    }

    /**
     * Met à jour un produit
     */
    public function update(string $id): void
    {
        $product = $this->productRepository->findById((int) $id);

        if (!$product) {
            $this->redirectWithMessage('/admin/products', 'error', 'Produit non trouvé.');
            return;
        }

        $data = $this->getPostData();

        $validator = new Validator($data);
        $validator
            ->required('name', 'Le nom est requis.')
            ->required('price', 'Le prix est requis.')
            ->numeric('price');

        if (!$validator->isValid()) {
            Session::flash('errors', $validator->getFirstErrors());
            Session::flash('old', $data);
            $this->redirect('/admin/products/' . $id . '/edit');
            return;
        }

        $this->fillProduct($product, $data);

        // Mettre à jour le slug si le nom a changé
        $newSlug = Product::generateSlug($product->name);
        if ($newSlug !== $product->slug) {
            $counter = 1;
            while ($this->productRepository->slugExists($newSlug, $product->id)) {
                $newSlug = Product::generateSlug($product->name) . '-' . $counter;
                $counter++;
            }
            $product->slug = $newSlug;
        }

        $this->productRepository->update($product);

        // Gérer les tailles
        if (isset($data['sizes'])) {
            $this->updateSizes($product->id, $data['sizes']);
        }

        Session::flash('success', 'Produit modifié avec succès.');
        $this->redirect('/admin/products');
    }

    /**
     * Supprime un produit
     */
    public function destroy(string $id): void
    {
        $product = $this->productRepository->findById((int) $id);

        if (!$product) {
            $this->redirectWithMessage('/admin/products', 'error', 'Produit non trouvé.');
            return;
        }

        $this->productRepository->delete((int) $id);
        Session::flash('success', 'Produit supprimé avec succès.');
        $this->redirect('/admin/products');
    }

    /**
     * Remplit le produit avec les données du formulaire
     */
    private function fillProduct(Product $product, array $data): void
    {
        $product->name = $data['name'];
        $product->description = $data['description'] ?? null;
        $product->short_description = $data['short_description'] ?? null;
        $product->price = (float) $data['price'];
        $product->sale_price = !empty($data['sale_price']) ? (float) $data['sale_price'] : null;
        $product->stock = (int) ($data['stock'] ?? 0);
        $product->sku = $data['sku'] ?? null;
        $product->image = !empty($data['image']) ? $data['image'] : null;
        $product->category_id = !empty($data['category_id']) ? (int) $data['category_id'] : null;
        $product->brand = $data['brand'] ?? null;
        $product->is_active = isset($data['is_active']);
        $product->is_featured = isset($data['is_featured']);
    }

    /**
     * Met à jour les tailles d'un produit
     */
    private function updateSizes(int $productId, array $sizesData): void
    {
        $sizes = [];
        $sizeNames = $sizesData['size'] ?? [];
        $sizeStocks = $sizesData['stock'] ?? [];

        foreach ($sizeNames as $index => $sizeName) {
            if (!empty(trim($sizeName))) {
                $sizes[] = [
                    'size' => trim($sizeName),
                    'stock' => (int) ($sizeStocks[$index] ?? 0),
                ];
            }
        }

        $this->productRepository->updateSizes($productId, $sizes);
    }
}
