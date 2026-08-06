<?php

declare(strict_types=1);

namespace App\Helpers;

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

        return $uri ?: '/';
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