<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Repository\CategoryRepository;
use App\Models\Category;

/**
 * Contrôleur admin pour les catégories
 */
class CategoryController extends Controller
{
    private CategoryRepository $categoryRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * Liste des catégories
     */
    public function index(): void
    {
        $categories = $this->categoryRepository->findAll(false);

        $this->render('admin/categories/index', [
            'title' => 'Catégories',
            'categories' => $categories,
        ], 'admin');
    }

    /**
     * Formulaire de création
     */
    public function create(): void
    {
        $categories = $this->categoryRepository->findAll(false);

        $this->render('admin/categories/form', [
            'title' => 'Nouvelle catégorie',
            'category' => null,
            'categories' => $categories,
        ], 'admin');
    }

    /**
     * Enregistre une nouvelle catégorie
     */
    public function store(): void
    {
        $data = $this->getPostData();

        $validator = new Validator($data);
        $validator->required('name', 'Le nom est requis.');

        if (!$validator->isValid()) {
            Session::flash('errors', $validator->getFirstErrors());
            Session::flash('old', $data);
            $this->redirect('/admin/categories/create');
            return;
        }

        $category = new Category();
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        $category->parent_id = !empty($data['parent_id']) ? (int) $data['parent_id'] : null;
        $category->sort_order = (int) ($data['sort_order'] ?? 0);
        $category->is_active = isset($data['is_active']);

        // Générer le slug
        $category->slug = Category::generateSlug($category->name);
        $counter = 1;
        while ($this->categoryRepository->slugExists($category->slug)) {
            $category->slug = Category::generateSlug($category->name) . '-' . $counter;
            $counter++;
        }

        $this->categoryRepository->create($category);

        Session::flash('success', 'Catégorie créée avec succès.');
        $this->redirect('/admin/categories');
    }

    /**
     * Formulaire d'édition
     */
    public function edit(string $id): void
    {
        $category = $this->categoryRepository->findById((int) $id);

        if (!$category) {
            $this->redirectWithMessage('/admin/categories', 'error', 'Catégorie non trouvée.');
            return;
        }

        $categories = $this->categoryRepository->findAll(false);

        $this->render('admin/categories/form', [
            'title' => 'Modifier ' . $category->name,
            'category' => $category,
            'categories' => $categories,
        ], 'admin');
    }

    /**
     * Met à jour une catégorie
     */
    public function update(string $id): void
    {
        $category = $this->categoryRepository->findById((int) $id);

        if (!$category) {
            $this->redirectWithMessage('/admin/categories', 'error', 'Catégorie non trouvée.');
            return;
        }

        $data = $this->getPostData();

        $validator = new Validator($data);
        $validator->required('name', 'Le nom est requis.');

        if (!$validator->isValid()) {
            Session::flash('errors', $validator->getFirstErrors());
            Session::flash('old', $data);
            $this->redirect('/admin/categories/' . $id . '/edit');
            return;
        }

        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        $category->parent_id = !empty($data['parent_id']) ? (int) $data['parent_id'] : null;
        $category->sort_order = (int) ($data['sort_order'] ?? 0);
        $category->is_active = isset($data['is_active']);

        // Mettre à jour le slug si le nom a changé
        $newSlug = Category::generateSlug($category->name);
        if ($newSlug !== $category->slug) {
            $counter = 1;
            while ($this->categoryRepository->slugExists($newSlug, $category->id)) {
                $newSlug = Category::generateSlug($category->name) . '-' . $counter;
                $counter++;
            }
            $category->slug = $newSlug;
        }

        $this->categoryRepository->update($category);

        Session::flash('success', 'Catégorie modifiée avec succès.');
        $this->redirect('/admin/categories');
    }

    /**
     * Supprime une catégorie
     */
    public function destroy(string $id): void
    {
        $category = $this->categoryRepository->findById((int) $id);

        if (!$category) {
            $this->redirectWithMessage('/admin/categories', 'error', 'Catégorie non trouvée.');
            return;
        }

        $this->categoryRepository->delete((int) $id);
        Session::flash('success', 'Catégorie supprimée avec succès.');
        $this->redirect('/admin/categories');
    }
}
