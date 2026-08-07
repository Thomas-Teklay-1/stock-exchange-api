<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Application;
use App\Routing\Router;

Application::bootstrap();

$router = new Router();

require dirname(__DIR__) . '/routes/api.php';

$router->dispatch();