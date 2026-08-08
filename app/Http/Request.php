<?php

declare(strict_types=1);

namespace App\Http;

use App\Models\User;

class Request
{
    private static ?\App\Models\User $authenticatedUser = null;

    public static function setAuthenticatedUser(User $user): void
    {
        self::$authenticatedUser = $user;
    }

    public static function user(): ?User
    {
        return self::$authenticatedUser;
    }

    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public static function header(string $name): ?string
    {
        $serverKey = 'HTTP_' . strtoupper(
            str_replace('-', '_', $name)
        );

        if (isset($_SERVER[$serverKey])) {
            return trim($_SERVER[$serverKey]);
        }

        /*
         * Apache may expose the Authorization header through
         * REDIRECT_HTTP_AUTHORIZATION when using rewrite rules.
         */
        $redirectedServerKey = 'REDIRECT_' . $serverKey;

        if (isset($_SERVER[$redirectedServerKey])) {
            return trim($_SERVER[$redirectedServerKey]);
        }

        /*
         * Some Apache configurations expose Authorization through
         * getallheaders().
         */
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            foreach ($headers as $headerName => $value) {
                if (strcasecmp($headerName, $name) === 0) {
                    return trim($value);
                }
            }
        }

        return null;
    }

    public static function uri(): string
    {
        $uri = parse_url(
            $_SERVER['REQUEST_URI'] ?? '/',
            PHP_URL_PATH
        );

        $uri = $uri ?: '/';

        $basePath = self::basePath();

        if (
            $basePath !== '/' &&
            str_starts_with($uri, $basePath)
        ) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = '/' . ltrim($uri, '/');

        return $uri === '//' ? '/' : $uri;
    }

    private static function basePath(): string
    {
        if (!empty($_ENV['APP_BASE_PATH'])) {
            return rtrim($_ENV['APP_BASE_PATH'], '/');
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        $basePath = dirname($scriptName);

        if ($basePath === '\\' || $basePath === '.') {
            return '';
        }

        return rtrim(
            str_replace('\\', '/', $basePath),
            '/'
        );
    }

    public static function input(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $data = json_decode(
                file_get_contents('php://input'),
                true
            );

            return is_array($data) ? $data : [];
        }

        return $_POST;
    }

    public static function query(): array
    {
        return $_GET;
    }
}