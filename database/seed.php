<?php

/**
 * Script de seeding de la base de données
 * Insère des données de test pour le développement
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Faker\Factory;

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
if (file_exists(dirname(__DIR__) . '/.env')) {
    $dotenv->load();
}

echo "=== Seeding de la base de données ===\n\n";

// Configuration
$config = require __DIR__ . '/../config/database.php';
$faker = Factory::create('fr_FR');

try {
    $dsn = sprintf(
        '%s:host=%s;port=%s;dbname=%s',
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['database']
    );

    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);

    // ============================================
    // Utilisateurs
    // ============================================
    echo "Création des utilisateurs...\n";

    // Admin
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("
        INSERT INTO users (email, password, first_name, last_name, role)
        VALUES ('admin@footballshop.fr', '{$adminPassword}', 'Admin', 'Football', 'admin')
        ON CONFLICT (email) DO NOTHING
    ");
    echo "  ✓ Utilisateur admin créé (admin@footballshop.fr / admin123)\n";

    // Clients de test
    $customerPassword = password_hash('customer123', PASSWORD_DEFAULT);
    for ($i = 0; $i < 5; $i++) {
        $email = $faker->unique()->safeEmail();
        $firstName = $faker->firstName();
        $lastName = $faker->lastName();
        $phone = $faker->phoneNumber();
        $address = $faker->streetAddress();
        $city = $faker->city();
        $postalCode = $faker->postcode();

        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, first_name, last_name, phone, address, city, postal_code, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'customer')
            ON CONFLICT (email) DO NOTHING
        ");
        $stmt->execute([$email, $customerPassword, $firstName, $lastName, $phone, $address, $city, $postalCode]);
    }
    echo "  ✓ 5 clients de test créés (mot de passe: customer123)\n";

    // ============================================
    // Catégories
    // ============================================
    echo "\nCréation des catégories...\n";

    $categories = [
        ['name' => 'Maillots', 'slug' => 'maillots', 'description' => 'Maillots de football officiels et répliques'],
        ['name' => 'Chaussures', 'slug' => 'chaussures', 'description' => 'Crampons et chaussures de football'],
        ['name' => 'Équipements', 'slug' => 'equipements', 'description' => 'Équipements et accessoires de football'],
        ['name' => 'Ballons', 'slug' => 'ballons', 'description' => 'Ballons de football officiels et entraînement'],
        ['name' => 'Gardien', 'slug' => 'gardien', 'description' => 'Équipement spécial gardien de but'],
    ];

    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("
            INSERT INTO categories (name, slug, description, is_active)
            VALUES (?, ?, ?, true)
            ON CONFLICT (slug) DO NOTHING
        ");
        $stmt->execute([$cat['name'], $cat['slug'], $cat['description']]);
    }
    echo "  ✓ " . count($categories) . " catégories créées\n";

    // Récupérer les IDs des catégories
    $categoryIds = [];
    $stmt = $pdo->query("SELECT id, slug FROM categories");
    while ($row = $stmt->fetch()) {
        $categoryIds[$row['slug']] = $row['id'];
    }

    // ============================================
    // Produits
    // ============================================
    echo "\nCréation des produits...\n";

    $products = [
        // Maillots
        [
            'name' => 'Maillot PSG Domicile 2024',
            'slug' => 'maillot-psg-domicile-2024',
            'description' => 'Maillot officiel du Paris Saint-Germain pour la saison 2024. Design élégant avec les couleurs emblématiques bleu et rouge.',
            'short_description' => 'Maillot officiel PSG saison 2024',
            'price' => 89.99,
            'stock' => 50,
            'sku' => 'MAI-PSG-DOM-24',
            'category_id' => $categoryIds['maillots'],
            'brand' => 'Nike',
            'is_featured' => true,
            'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
        ],
        [
            'name' => 'Maillot OM Domicile 2024',
            'slug' => 'maillot-om-domicile-2024',
            'description' => 'Maillot officiel de l\'Olympique de Marseille. Le blanc emblématique avec finitions modernes.',
            'short_description' => 'Maillot officiel OM saison 2024',
            'price' => 84.99,
            'stock' => 45,
            'sku' => 'MAI-OM-DOM-24',
            'category_id' => $categoryIds['maillots'],
            'brand' => 'Puma',
            'is_featured' => true,
            'sizes' => ['S', 'M', 'L', 'XL'],
        ],
        [
            'name' => 'Maillot France Domicile 2024',
            'slug' => 'maillot-france-domicile-2024',
            'description' => 'Maillot officiel de l\'Équipe de France. Design moderne bleu nuit avec détails dorés.',
            'short_description' => 'Maillot officiel Équipe de France',
            'price' => 94.99,
            'stock' => 60,
            'sku' => 'MAI-FRA-DOM-24',
            'category_id' => $categoryIds['maillots'],
            'brand' => 'Nike',
            'is_featured' => true,
            'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
        ],
        [
            'name' => 'Maillot Authentique FC Barcelone Domicile 25/26',
            'slug' => 'maillot-authentique-FC-barcelone-domicile-25/26',
            'description' => 'La technologie innovante AEROREADY évacue l\'humidité de votre corps, vous laissant ainsi à l\'aise, au sec et au frais',
            'short_description' => 'Découvrez le maillot légendaire des meringués de la saison 25/26.',
            'price' => 110,
            'stock' => 51,
            'sku' => 'REA-MAD-MAI-DOM',
            'category_id' => $categoryIds['maillots'],
            'brand' => 'Adidas',
            'is_featured' => true,
            'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
        ],
        [
            'name' => 'Real Madrid Maillot Domicile 25/26',
            'slug' => 'real-madrid-maillot-domicile-25/26',
            'description' => 'Maillot du FC Barcelone de la Saison 25/26',
            'short_description' => 'Le maillot domicile 2025/26 du FC Barcelone actualise l\'un des looks les plus reconnaissables du football.',
            'price' => 120,
            'stock' => 57,
            'sku' => 'MAI-AUT-FC-BAR',
            'category_id' => $categoryIds['maillots'],
            'brand' => 'Nike',
            'is_featured' => true,
            'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
        ],

        // Chaussures
        [
            'name' => 'Nike Mercurial Vapor 15',
            'slug' => 'nike-mercurial-vapor-15',
            'description' => 'Crampons de vitesse avec technologie Flyknit. Parfaits pour les joueurs rapides.',
            'short_description' => 'Crampons haute performance',
            'price' => 249.99,
            'stock' => 25,
            'sku' => 'CHA-NIK-MV15',
            'category_id' => $categoryIds['chaussures'],
            'brand' => 'Nike',
            'is_featured' => true,
            'sizes' => ['39', '40', '41', '42', '43', '44', '45'],
        ],
        [
            'name' => 'Adidas Predator Edge',
            'slug' => 'adidas-predator-edge',
            'description' => 'Les crampons préférés des milieux créatifs. Zone de contrôle optimisée.',
            'short_description' => 'Crampons pour le contrôle',
            'price' => 229.99,
            'stock' => 30,
            'sku' => 'CHA-ADI-PRED',
            'category_id' => $categoryIds['chaussures'],
            'brand' => 'Adidas',
            'is_featured' => false,
            'sizes' => ['39', '40', '41', '42', '43', '44'],
        ],
        [
            'name' => 'Puma Future Ultimate',
            'slug' => 'puma-future-ultimate',
            'description' => 'Crampons innovants avec système de laçage adaptatif.',
            'short_description' => 'Crampons nouvelle génération',
            'price' => 219.99,
            'sale_price' => 179.99,
            'stock' => 20,
            'sku' => 'CHA-PUM-FUT',
            'category_id' => $categoryIds['chaussures'],
            'brand' => 'Puma',
            'is_featured' => false,
            'sizes' => ['40', '41', '42', '43', '44'],
        ],

        // Équipements
        [
            'name' => 'Protège-tibias Nike',
            'slug' => 'protege-tibias-nike',
            'description' => 'Protège-tibias légers avec mousse EVA pour une protection optimale.',
            'short_description' => 'Protection légère et efficace',
            'price' => 24.99,
            'stock' => 100,
            'sku' => 'EQU-NIK-PROT',
            'category_id' => $categoryIds['equipements'],
            'brand' => 'Nike',
            'is_featured' => false,
            'sizes' => ['S', 'M', 'L'],
        ],
        [
            'name' => 'Sac de sport Adidas',
            'slug' => 'sac-sport-adidas',
            'description' => 'Sac de sport spacieux avec compartiment chaussures séparé.',
            'short_description' => 'Sac spacieux et pratique',
            'price' => 49.99,
            'stock' => 40,
            'sku' => 'EQU-ADI-SAC',
            'category_id' => $categoryIds['equipements'],
            'brand' => 'Adidas',
            'is_featured' => false,
            'sizes' => [],
        ],

        // Ballons
        [
            'name' => 'Ballon Nike Flight',
            'slug' => 'ballon-nike-flight',
            'description' => 'Ballon officiel avec technologie Aerowsculpt pour une trajectoire stable.',
            'short_description' => 'Ballon officiel compétition',
            'price' => 159.99,
            'stock' => 35,
            'sku' => 'BAL-NIK-FLI',
            'category_id' => $categoryIds['ballons'],
            'brand' => 'Nike',
            'is_featured' => true,
            'sizes' => ['5'],
        ],
        [
            'name' => 'Ballon Adidas UCL',
            'slug' => 'ballon-adidas-ucl',
            'description' => 'Ballon officiel UEFA Champions League. Design étoilé emblématique.',
            'short_description' => 'Ballon officiel Champions League',
            'price' => 149.99,
            'stock' => 30,
            'sku' => 'BAL-ADI-UCL',
            'category_id' => $categoryIds['ballons'],
            'brand' => 'Adidas',
            'is_featured' => true,
            'sizes' => ['5'],
        ],
        [
            'name' => 'Ballon LDC Finale a Munich',
            'slug' => 'ballon-ldc-finale-munich',
            'description' => 'Inspiré par les tons verts des dômes cuivrés de Munich et ses décors dignes d\'une carte postale, ce ballon adidas est un hommage élégant à l\'hôte de la finale de l\'UEFA Champions League 24/25.',
            'short_description' => 'Ballon LDC 2024/25, finale PSG-Inter',
            'price' => 60,
            'stock' => 28,
            'sku' => 'BAL-LDC-FIN-MUN',
            'category_id' => $categoryIds['ballons'],
            'brand' => 'Adidas',
            'is_featured' => true,
            'sizes' => ['5'],
        ],
        [
            'name' => 'Ballon LDC Finale a Kiev',
            'slug' => 'ballon-ldc-finale-kiev',
            'description' => 'Le ballon de la LDC de la finale en Russie de la saison 2018/2019. Redécouvrez la finale entre le Réal Madrid et Liverpool.',
            'short_description' => 'Ballon LDC 2018/19, finale Real-Liverpool',
            'price' => 29.99,
            'stock' => 30,
            'sku' => 'BAL-LDC-FIN-KIE',
            'category_id' => $categoryIds['ballons'],
            'brand' => 'Adidas',
            'is_featured' => true,
            'sizes' => ['5'],
        ],

        // Gardien
        [
            'name' => 'Gants Uhlsport Supergrip',
            'slug' => 'gants-uhlsport-supergrip',
            'description' => 'Gants de gardien professionnels avec mousse Supergrip pour une adhérence maximale.',
            'short_description' => 'Gants pro haute adhérence',
            'price' => 89.99,
            'stock' => 25,
            'sku' => 'GAR-UHL-SUP',
            'category_id' => $categoryIds['gardien'],
            'brand' => 'Uhlsport',
            'is_featured' => false,
            'sizes' => ['7', '8', '9', '10', '11'],
        ],
        [
            'name' => 'Maillot Gardien Nike',
            'slug' => 'maillot-gardien-nike',
            'description' => 'Maillot de gardien avec renforts coudes et technologie Dri-FIT.',
            'short_description' => 'Maillot gardien avec renforts',
            'price' => 69.99,
            'stock' => 20,
            'sku' => 'GAR-NIK-MAI',
            'category_id' => $categoryIds['gardien'],
            'brand' => 'Nike',
            'is_featured' => false,
            'sizes' => ['S', 'M', 'L', 'XL'],
        ],
    ];

    foreach ($products as $product) {
        $sizes = $product['sizes'] ?? [];
        unset($product['sizes']);

        $columns = implode(', ', array_keys($product));
        $placeholders = implode(', ', array_fill(0, count($product), '?'));

        $stmt = $pdo->prepare("
            INSERT INTO products ({$columns})
            VALUES ({$placeholders})
            ON CONFLICT (slug) DO NOTHING
            RETURNING id
        ");
        $stmt->execute(array_values($product));
        $productId = $stmt->fetchColumn();

        // Ajouter les tailles
        if ($productId && !empty($sizes)) {
            foreach ($sizes as $size) {
                $stock = rand(5, 20);
                $stmt = $pdo->prepare("
                    INSERT INTO product_sizes (product_id, size, stock)
                    VALUES (?, ?, ?)
                    ON CONFLICT (product_id, size) DO NOTHING
                ");
                $stmt->execute([$productId, $size, $stock]);
            }
        }
    }
    echo "  ✓ " . count($products) . " produits créés avec leurs tailles\n";

    echo "\n=== Seeding terminé avec succès ===\n";
    echo "\nComptes de test:\n";
    echo "  Admin: admin@footballshop.fr / admin123\n";
    echo "  Client: (voir les emails générés) / customer123\n";

} catch (PDOException $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
    exit(1);
}
