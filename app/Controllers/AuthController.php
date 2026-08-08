<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\JsonResponse;
use App\Http\Request;
use App\Middleware\AuthenticationMiddleware;
use App\Repositories\SessionRepository;
use App\Repositories\UserRepository;
use App\Services\AuthenticationService;
use InvalidArgumentException;
use Throwable;

class AuthController
{
    private AuthenticationService $authenticationService;

    public function __construct()
    {
        $this->authenticationService = new AuthenticationService(
            new UserRepository(),
            new SessionRepository()
        );
    }

    public function register(): void
    {
        $input = Request::input();

        $role = $input['role'] ?? '';
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $phone = $input['phone'] ?? null;

        try {
            $user = $this->authenticationService->register(
                role: (string) $role,
                name: (string) $name,
                email: (string) $email,
                password: (string) $password,
                phone: $phone !== null
                    ? (string) $phone
                    : null
            );

            JsonResponse::success(
                data: [
                    'user' => $user->toArray(),
                ],
                message: 'Registration successful.',
                statusCode: 201
            );
        } catch (InvalidArgumentException $exception) {
            JsonResponse::error(
                message: $exception->getMessage(),
                statusCode: 422
            );
        } catch (Throwable $exception) {
            JsonResponse::error(
                message: 'Unable to register user.',
                statusCode: 500
            );
        }
    }

    public function login(): void
    {
        $input = Request::input();

        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        try {
            $result = $this->authenticationService->authenticate(
                email: (string) $email,
                password: (string) $password
            );

            JsonResponse::success(
                data: [
                    'user' => $result['user']->toArray(),
                    'token' => $result['token'],
                    'expires_at' => $result['expires_at'],
                ],
                message: 'Login successful.'
            );
        } catch (InvalidArgumentException $exception) {
            JsonResponse::error(
                message: $exception->getMessage(),
                statusCode: 401
            );
        } catch (Throwable $exception) {
            JsonResponse::error(
                message: 'Unable to authenticate user.',
                statusCode: 500
            );
        }
    }

    public function logout(): void
    {
        $token = $this->getBearerToken();

        if ($token === null) {
            JsonResponse::error(
                message: 'Authentication token is required.',
                statusCode: 401
            );
        }

        try {
            $this->authenticationService->logout($token);

            JsonResponse::success(
                data: [],
                message: 'Logout successful.'
            );
        } catch (Throwable $exception) {
            JsonResponse::error(
                message: 'Unable to log out.',
                statusCode: 500
            );
        }
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

    public function me(): void
    {
        $middleware = new \App\Middleware\AuthenticationMiddleware();

        $user = $middleware->authenticate();

        JsonResponse::success(
            data: [
                'user' => $user->toArray(),
            ],
            message: 'Authenticated user.'
        );
    }
}