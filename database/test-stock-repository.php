<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Application;
use App\Repositories\StockRepository;

Application::bootstrap();

$repository = new StockRepository();

echo "Testing stock repository...\n\n";

$stocks = $repository->findAll();

echo "Stocks found: " . count($stocks) . "\n\n";

print_r($stocks);

echo "\nRepository test completed successfully.\n";