<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\JsonResponse;

class HealthController
{
    public function index(): void
    {
        JsonResponse::success(
            [
                'status' => 'healthy'
            ],
            'Stock Exchange API is running.'
        );
    }
}