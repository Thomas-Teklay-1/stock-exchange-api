<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Application;
use App\Repositories\StockCategoryRepository;
use App\Repositories\StockRepository;
use App\Repositories\UserRepository;
use App\Services\StockService;

Application::bootstrap();

echo "Testing stock service...\n\n";

$users = new UserRepository();
$stocks = new StockRepository();
$categories = new StockCategoryRepository();

$service = new StockService(
    $stocks,
    $categories
);

/*
|--------------------------------------------------------------------------
| Find test seller
|--------------------------------------------------------------------------
*/

$seller = $users->findByEmail(
    'auth-test@example.com'
);

if ($seller === null) {
    throw new RuntimeException(
        'Authentication test seller was not found.'
    );
}

$sellerId = (int) $seller['user_id'];

echo "Seller found: {$sellerId}\n";

/*
|--------------------------------------------------------------------------
| Find category
|--------------------------------------------------------------------------
*/

$category = $categories->findByName(
    'Technology'
);

if ($category === null) {
    throw new RuntimeException(
        'Technology category was not found.'
    );
}

$categoryId = (int) $category['category_id'];

echo "Category found: {$categoryId}\n";

/*
|--------------------------------------------------------------------------
| Create stock
|--------------------------------------------------------------------------
*/

$stockId = $service->createStock(
    $sellerId,
    [
        'category_id' => $categoryId,
        'stock_name' => 'Service Test Stock',
        'description' => 'Temporary service test stock.',
        'quantity_total' => 500,
        'price_per_share' => 15.75,
    ]
);

echo "Stock created: {$stockId}\n";

/*
|--------------------------------------------------------------------------
| Retrieve stock
|--------------------------------------------------------------------------
*/

$stock = $service->getStock($stockId);

if ($stock['stock_name'] !== 'Service Test Stock') {
    throw new RuntimeException(
        'Created stock was not retrieved correctly.'
    );
}

if ((int) $stock['quantity_available'] !== 500) {
    throw new RuntimeException(
        'Initial available quantity is incorrect.'
    );
}

echo "Stock retrieval successful.\n";

/*
|--------------------------------------------------------------------------
| Retrieve seller stocks
|--------------------------------------------------------------------------
*/

$sellerStocks = $service->getSellerStocks(
    $sellerId
);

$found = false;

foreach ($sellerStocks as $sellerStock) {
    if ((int) $sellerStock['stock_id'] === $stockId) {
        $found = true;
        break;
    }
}

if (!$found) {
    throw new RuntimeException(
        'Created stock was not found in seller stocks.'
    );
}

echo "Seller stock retrieval successful.\n";

/*
|--------------------------------------------------------------------------
| Update stock
|--------------------------------------------------------------------------
*/

$service->updateStock(
    $stockId,
    $sellerId,
    [
        'category_id' => $categoryId,
        'stock_name' => 'Updated Service Test Stock',
        'description' => 'Updated service test stock.',
        'quantity_total' => 750,
        'price_per_share' => 20.00,
        'status' => 'active',
    ]
);

$updatedStock = $service->getStock(
    $stockId
);

if (
    $updatedStock['stock_name'] !==
    'Updated Service Test Stock'
) {
    throw new RuntimeException(
        'Stock update failed.'
    );
}

if (
    (int) $updatedStock['quantity_available'] !== 500
) {
    throw new RuntimeException(
        'Available quantity should not be changed by a normal stock update.'
    );
}

echo "Stock update successful.\n";
echo "Available quantity protection verified.\n";

/*
|--------------------------------------------------------------------------
| Delete stock
|--------------------------------------------------------------------------
*/

$service->deleteStock(
    $stockId,
    $sellerId
);

$deletedStock = null;

try {
    $deletedStock = $service->getStock(
        $stockId
    );
} catch (RuntimeException $exception) {
    $deletedStock = null;
}

if ($deletedStock !== null) {
    throw new RuntimeException(
        'Stock still exists after deletion.'
    );
}

echo "Stock deletion successful.\n\n";

echo "Stock service test passed successfully.\n";