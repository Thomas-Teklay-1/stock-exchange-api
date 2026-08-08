<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\JsonResponse;
use App\Http\Request;
use App\Models\User;
use App\Repositories\SessionRepository;
use App\Repositories\UserRepository;
use App\Services\AuthenticationService;

class AuthenticationMiddleware
{
    private AuthenticationService $authenticationService;

    public function __construct()
    {
        $this->authenticationService = new AuthenticationService(
            new UserRepository(),
            new SessionRepository()
        );
    }

    public function authenticate(): User
    {
        $token = $this->getBearerToken();

        if ($token === null) {
            JsonResponse::error(
                message: 'Authentication token is required.',
                statusCode: 401
            );
        }

        $user = $this->authenticationService
            ->getUserBySessionToken($token);

        if ($user === null) {
            JsonResponse::error(
                message: 'Invalid or expired authentication token.',
                statusCode: 401
            );
        }

        return $user;
    }

    private function getBearerToken(): ?string
    {
        $authorization = Request::header('Authorization');

        if ($authorization === null || $authorization === '') {
            return null;
        }

        if (!preg_match(
            '/^Bearer\s+(.+)$/i',
            $authorization,
            $matches
        )) {
            return null;
        }

        return trim($matches[1]);
    }
}