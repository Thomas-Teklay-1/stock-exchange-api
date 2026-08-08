<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Routing\Router;

return static function (Router $router): void {
    $router->post(
        '/api/auth/register',
        [AuthController::class, 'register']
    );

    $router->post(
        '/api/auth/login',
        [AuthController::class, 'login']
    );

    $router->post(
        '/api/auth/logout',
        [AuthController::class, 'logout']
    );
};