<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Config\App;

App::bootstrap();

header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Stock Exchange API is running.',
    'environment' => $_ENV['APP_ENV'] ?? 'unknown'
]);