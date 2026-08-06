<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Config\App;
use App\Config\Database;
use App\Helpers\JsonResponse;

App::bootstrap();

Database::getConnection();

JsonResponse::success(
    [],
    'Stock Exchange API is running.'
);