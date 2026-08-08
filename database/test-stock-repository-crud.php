<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Application;
use App\Repositories\StockRepository;
use App\Repositories\UserRepository;
use App\Repositories\StockCategoryRepository;

Application::bootstrap();

echo "Testing stock repository CRUD...\n\n";

$stocks = new StockRepository();
$users = new UserRepository();
$categories = new StockCategoryRepository();

/*
|--------------------------------------------------------------------------
| 1. Find a seller
|--------------------------------------------------------------------------
*/

$seller = $users->findByEmail('auth-test@example.com');

if ($seller === null) {
    throw new RuntimeException(
        'Test seller was not found. Run the authentication test first.'
    );
}

$sellerId = (int) $seller['user_id'];

echo "Seller found: {$sellerId}\n";

/*
|--------------------------------------------------------------------------
| 2. Find a stock category
|--------------------------------------------------------------------------
*/

$category = $categories->findByName('Technology');

if ($category === null) {
    throw new RuntimeException(
        'Technology category was not found.'
    );
}

$categoryId = (int) $category['category_id'];

echo "Category found: {$categoryId}\n";

/*
|--------------------------------------------------------------------------
| 3. Create temporary stock
|--------------------------------------------------------------------------
*/

$stockId = $stocks->create([
    'seller_id' => $sellerId,
    'category_id' => $categoryId,
    'stock_name' => 'Repository Test Stock',
    'description' => 'Temporary stock used for repository testing.',
    'quantity_total' => 100,
    'quantity_available' => 100,
    'price_per_share' => 25.50,
    'image_path' => null,
    'status' => 'active',
]);

echo "Stock created: {$stockId}\n";

/*
|--------------------------------------------------------------------------
| 4. Find stock by ID
|--------------------------------------------------------------------------
*/

$stock = $stocks->findById($stockId);

if ($stock === null) {
    throw new RuntimeException(
        'Created stock could not be retrieved.'
    );
}

echo "Stock retrieved successfully.\n";
echo "Stock name: {$stock['stock_name']}\n";

/*
|--------------------------------------------------------------------------
| 5. Update stock
|--------------------------------------------------------------------------
*/

$updated = $stocks->update(
    $stockId,
    [
        'category_id' => $categoryId,
        'stock_name' => 'Updated Repository Test Stock',
        'description' => 'Updated repository test stock.',
        'quantity_total' => 200,
        'quantity_available' => 200,
        'price_per_share' => 30.00,
        'image_path' => null,
        'status' => 'active',
    ]
);

if (!$updated) {
    throw new RuntimeException(
        'Stock update failed.'
    );
}

echo "Stock updated successfully.\n";

/*
|--------------------------------------------------------------------------
| 6. Verify update
|--------------------------------------------------------------------------
*/

$updatedStock = $stocks->findById($stockId);

if ($updatedStock === null) {
    throw new RuntimeException(
        'Updated stock could not be retrieved.'
    );
}

if (
    $updatedStock['stock_name'] !==
    'Updated Repository Test Stock'
) {
    throw new RuntimeException(
        'Stock name was not updated correctly.'
    );
}

if (
    (int) $updatedStock['quantity_total'] !== 200
) {
    throw new RuntimeException(
        'Stock quantity was not updated correctly.'
    );
}

echo "Stock update verified successfully.\n";

/*
|--------------------------------------------------------------------------
| 7. Delete stock
|--------------------------------------------------------------------------
*/

$deleted = $stocks->delete($stockId);

if (!$deleted) {
    throw new RuntimeException(
        'Stock deletion failed.'
    );
}

echo "Stock deleted successfully.\n";

/*
|--------------------------------------------------------------------------
| 8. Verify deletion
|--------------------------------------------------------------------------
*/

$deletedStock = $stocks->findById($stockId);

if ($deletedStock !== null) {
    throw new RuntimeException(
        'Stock still exists after deletion.'
    );
}

echo "Stock deletion verified successfully.\n\n";

echo "Stock repository CRUD test passed successfully.\n";