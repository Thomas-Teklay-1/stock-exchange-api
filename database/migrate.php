<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Application;
use Database\MigrationRunner;

Application::bootstrap();

$runner = new MigrationRunner();

$runner->migrate();