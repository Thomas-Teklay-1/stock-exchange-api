<?php

declare(strict_types=1);

use App\Controllers\HealthController;
use App\Routing\Router;

/** @var Router $router */

$router->get(
    '/',
    [HealthController::class, 'index']
);