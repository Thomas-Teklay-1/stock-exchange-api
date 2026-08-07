<?php

declare(strict_types=1);

namespace App\Http;

class JsonResponse
{
    public static function send(
        mixed $data,
        int $statusCode = 200
    ): never {
        http_response_code($statusCode);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        exit;
    }

    public static function success(
        mixed $data = [],
        string $message = 'Success',
        int $statusCode = 200
    ): never {
        self::send([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function error(
        string $message,
        int $statusCode = 400,
        mixed $errors = null
    ): never {
        self::send([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}