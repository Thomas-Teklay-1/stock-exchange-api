<?php

declare(strict_types=1);

namespace App\Http;

class Request
{
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
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

        return rtrim(str_replace('\\', '/', $basePath), '/');
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