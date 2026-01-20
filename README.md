Documentation Technique - Football Shop
Vue d'ensemble
Football Shop est une application e-commerce développée en PHP 8 avec une architecture MVC personnalisée. Elle utilise PostgreSQL comme base de données et Vue.js pour les interactions frontend dynamiques.

Stack Technique
Composant	Technologie	Version
Backend	PHP	8.1+
Base de données	PostgreSQL	14+
Frontend	Vue.js	3.x
CSS	TailwindCSS	CDN
Autoloading	Composer PSR-4	-
Variables d'env	vlucas/phpdotenv	5.6
Architecture
Pattern MVC
L'application suit le pattern Model-View-Controller :

Requête HTTP → Router → Controller → Repository → Model
                          ↓
                         View ← Données
Structure des namespaces
App\
├── Controllers\        # Contrôleurs (gèrent les requêtes)
│   └── Admin\          # Contrôleurs administration
├── Core\               # Framework (Router, Database, etc.)
├── Middleware\         # Middlewares (Auth, Admin)
├── Models\             # Entités/Modèles
├── Repository\         # Accès aux données
└── Services\           # Logique métier
Core Framework
Application (src/Core/Application.php)
Singleton principal de l'application. Responsabilités :

Chargement des variables d'environnement (dotenv)
Chargement de la configuration
Initialisation des sessions
Gestion des exceptions
$app = Application::getInstance();
$app->config('database.host'); // Accès configuration
$app->router()->get('/path', [Controller::class, 'method']);
$app->run();
Router (src/Core/Router.php)
Système de routage avec support des paramètres dynamiques.

// Routes simples
$router->get('/products', [ProductController::class, 'index']);

// Paramètres dynamiques
$router->get('/products/{slug}', [ProductController::class, 'show']);

// Avec middlewares
$router->get('/admin', [DashboardController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);

// Méthodes supportées
$router->get($path, $handler, $middlewares);
$router->post($path, $handler, $middlewares);
$router->put($path, $handler, $middlewares);
$router->delete($path, $handler, $middlewares);
Database (src/Core/Database.php)
Singleton PDO avec méthodes utilitaires.

// Requête préparée
$stmt = Database::query("SELECT * FROM products WHERE id = ?", [$id]);

// Récupérer une ligne
$product = Database::fetchOne("SELECT * FROM products WHERE id = ?", [$id]);

// Récupérer toutes les lignes
$products = Database::fetchAll("SELECT * FROM products WHERE is_active = true");

// Insérer (retourne l'ID)
$id = Database::insert('products', [
    'name' => 'Produit',
    'price' => 99.99
]);

// Mettre à jour
Database::update('products', ['price' => 79.99], 'id = ?', [$id]);

// Supprimer
Database::delete('products', 'id = ?', [$id]);

// Transactions
Database::beginTransaction();
try {
    // ... opérations
    Database::commit();
} catch (Exception $e) {
    Database::rollback();
}
Controller (src/Core/Controller.php)
Classe de base pour tous les contrôleurs.

class ProductController extends Controller
{
    public function show(string $slug): void
    {
        // Rendre une vue avec layout
        $this->render('products/show', [
            'title' => 'Mon produit',
            'product' => $product
        ]);

        // Réponse JSON (API)
        $this->json(['success' => true, 'data' => $data]);

        // Redirection
        $this->redirect('/products');

        // Redirection avec message flash
        $this->redirectWithMessage('/products', 'success', 'Produit créé !');

        // Récupérer les données
        $post = $this->getPostData();    // $_POST
        $query = $this->getQueryData();  // $_GET
        $json = $this->getJsonData();    // Corps JSON

        // Utilisateur
        $user = $this->getCurrentUser();
        $this->isAuthenticated();
        $this->isAdmin();
    }
}
Session (src/Core/Session.php)
Gestion des sessions avec messages flash.

// Stockage
Session::set('key', 'value');
$value = Session::get('key', 'default');
Session::has('key');
Session::remove('key');

// Messages flash (affichés une seule fois)
Session::flash('success', 'Opération réussie !');
$message = Session::getFlash('success');

// Sécurité
Session::regenerate(); // Régénère l'ID
Session::destroy();    // Détruit la session
Validator (src/Core/Validator.php)
Validation des données entrantes.

$validator = new Validator($_POST);

$validator
    ->required('email', 'L\'email est requis')
    ->email('email')
    ->required('password')
    ->minLength('password', 8)
    ->matches('password_confirm', 'password');

if ($validator->isValid()) {
    // Données valides
} else {
    $errors = $validator->getFirstErrors();
    // ['email' => 'Message', 'password' => 'Message']
}
Règles disponibles :

required($field, $message)
email($field, $message)
minLength($field, $min, $message)
maxLength($field, $max, $message)
matches($field, $otherField, $message)
numeric($field, $message)
min($field, $value, $message)
max($field, $value, $message)
regex($field, $pattern, $message)
in($field, $values, $message)
positiveInteger($field, $message)
Modèles
Structure d'un modèle
class Product
{
    public ?int $id = null;
    public string $name;
    public float $price;
    // ...

    // Créer depuis un tableau (résultat BDD)
    public static function fromArray(array $data): self;

    // Convertir en tableau
    public function toArray(): array;

    // Méthodes métier
    public function getEffectivePrice(): float;
    public function isOnSale(): bool;
}
Modèles disponibles
Modèle	Description
User	Utilisateurs (clients/admins)
Category	Catégories de produits
Product	Produits du catalogue
Cart	Panier d'achat
CartItem	Article dans le panier
Order	Commande
OrderItem	Article dans une commande
Repositories
Pattern Repository pour l'accès aux données.

class ProductRepository
{
    // Recherche
    public function findById(int $id): ?Product;
    public function findBySlug(string $slug): ?Product;
    public function findAll(array $filters, int $limit, int $offset): array;
    public function count(array $filters): int;

    // CRUD
    public function create(Product $product): int;
    public function update(Product $product): bool;
    public function delete(int $id): bool;

    // Spécifiques
    public function findFeatured(int $limit): array;
    public function getBrands(): array;
}
Middlewares
AuthMiddleware
Vérifie que l'utilisateur est connecté.

// Redirige vers /login si non connecté
$router->get('/orders', [OrderController::class, 'index'], [AuthMiddleware::class]);
AdminMiddleware
Vérifie que l'utilisateur est administrateur.

// Redirige vers / si non admin
$router->get('/admin', [DashboardController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Base de données
Schéma
users              → Utilisateurs
categories         → Catégories de produits
products           → Produits
product_sizes      → Tailles des produits
carts              → Paniers
cart_items         → Articles dans les paniers
orders             → Commandes
order_items        → Articles dans les commandes
Relations
users 1───N orders
users 1───N carts

categories 1───N products
products 1───N product_sizes

carts 1───N cart_items
cart_items N───1 products
cart_items N───1 product_sizes

orders 1───N order_items
order_items N───1 products
API REST
Endpoints pour le panier (utilisés par Vue.js) :

Méthode	URL	Description
GET	/api/cart	Contenu du panier
POST	/api/cart/add	Ajouter un produit
POST	/api/cart/update	Modifier quantité
POST	/api/cart/remove	Supprimer un article
GET	/api/cart/count	Nombre d'articles
Format des requêtes/réponses
// POST /api/cart/add
{
    "product_id": 1,
    "size_id": 2,      // optionnel
    "quantity": 1
}

// Réponse
{
    "success": true,
    "count": 3,
    "subtotal": 149.97
}
Frontend (Vue.js)
Vue.js est utilisé pour :

Le panier dynamique
L'ajout au panier sur la fiche produit
Exemple de composant
<div id="cart-app" v-cloak>
    <div v-for="item in items" :key="item.id">
        {{ item.name }} - {{ item.quantity }}
    </div>
</div>

<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            items: [],
            loading: true
        };
    },
    mounted() {
        this.loadCart();
    },
    methods: {
        async loadCart() {
            const response = await fetch('/api/cart');
            const data = await response.json();
            this.items = data.items;
        }
    }
}).mount('#cart-app');
</script>
Sécurité
Mesures implémentées
Requêtes préparées : Protection contre les injections SQL
Échappement HTML : htmlspecialchars() dans les vues
Hachage des mots de passe : password_hash() / password_verify()
Régénération de session : Après connexion
Validation des entrées : Classe Validator
Middlewares : Vérification des droits d'accès
Points d'attention
Les uploads d'images ne sont pas implémentés (placeholder utilisé)
Protection CSRF non implémentée (à ajouter pour la production)
Rate limiting non implémenté
Tests manuels
Parcours client
Accéder à la page d'accueil
Naviguer dans le catalogue
Filtrer par catégorie/prix/marque
Voir le détail d'un produit
Ajouter au panier (sélectionner une taille si nécessaire)
Modifier le panier
Créer un compte / Se connecter
Passer une commande
Voir l'historique des commandes
Parcours admin
Se connecter avec un compte admin
Accéder au dashboard /admin
Gérer les produits (CRUD)
Gérer les catégories
Voir les commandes et modifier les statuts
Gérer les utilisateurs
Évolutions possibles
 Upload d'images pour les produits
 Système de wishlist
 Avis et notes sur les produits
 Intégration paiement réel (Stripe)
 Emails transactionnels
 Recherche avancée (Elasticsearch)
 Cache (Redis)
 Tests unitaires (PHPUnit)
 Protection CSRF
