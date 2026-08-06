<?php

declare(strict_types=1);

namespace App\Config;

use Dotenv\Dotenv;

class App
{
    public static function bootstrap(): void
    {
        $rootPath = dirname(__DIR__, 2);

        $dotenv = Dotenv::createImmutable($rootPath);
        $dotenv->safeLoad();

        date_default_timezone_set(
            $_ENV['TIMEZONE'] ?? 'UTC'
        );
    }
}