<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Config\App;
use App\Config\Database;

App::bootstrap();

Database::getConnection();

header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Database connection established successfully.',
    'environment' => $_ENV['APP_ENV'] ?? 'unknown'
]);