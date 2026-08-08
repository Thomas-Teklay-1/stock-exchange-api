<?php

declare(strict_types=1);

namespace App\Routing;

use App\Http\JsonResponse;
use App\Http\Request;

class Router
{
    /**
     * @var array<string, array<string, array{
     *     action: callable|array,
     *     middleware: array<int, string|object>
     * }>>
     */
    private array $routes = [];

    public function get(
        string $uri,
        callable|array $action,
        array $middleware = []
    ): void {
        $this->addRoute(
            'GET',
            $uri,
            $action,
            $middleware
        );
    }

    public function post(
        string $uri,
        callable|array $action,
        array $middleware = []
    ): void {
        $this->addRoute(
            'POST',
            $uri,
            $action,
            $middleware
        );
    }

    public function put(
        string $uri,
        callable|array $action,
        array $middleware = []
    ): void {
        $this->addRoute(
            'PUT',
            $uri,
            $action,
            $middleware
        );
    }

    public function delete(
        string $uri,
        callable|array $action,
        array $middleware = []
    ): void {
        $this->addRoute(
            'DELETE',
            $uri,
            $action,
            $middleware
        );
    }

    private function addRoute(
        string $method,
        string $uri,
        callable|array $action,
        array $middleware = []
    ): void {
        $this->routes[$method][$uri] = [
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(): void
    {
        $method = Request::method();
        $uri = Request::uri();

        $route = $this->routes[$method][$uri] ?? null;

        if ($route === null) {
            JsonResponse::error(
                'Route not found.',
                404
            );
        }

        $this->runMiddleware(
            $route['middleware']
        );

        $this->runAction(
            $route['action']
        );
    }

    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $middlewareClass) {
            $instance = is_object($middlewareClass)
                ? $middlewareClass
                : new $middlewareClass();

            if (!method_exists($instance, 'handle')) {
                JsonResponse::error(
                    'Invalid middleware configuration.',
                    500
                );
            }

            $instance->handle();
        }
    }

    private function runAction(callable|array $action): void
    {
        if (is_callable($action)) {
            $action();
            return;
        }

        [$controller, $controllerMethod] = $action;

        $instance = new $controller();

        $instance->$controllerMethod();
    }
}