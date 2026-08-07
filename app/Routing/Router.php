<?php

declare(strict_types=1);

namespace App\Routing;

use App\Http\JsonResponse;
use App\Http\Request;

class Router
{
    /**
     * @var array<string, array<string, callable|array>>
     */
    private array $routes = [];

    public function get(string $uri, callable|array $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, callable|array $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, callable|array $action): void
    {
        $this->addRoute('PUT', $uri, $action);
    }

    public function delete(string $uri, callable|array $action): void
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    private function addRoute(
        string $method,
        string $uri,
        callable|array $action
    ): void {
        $this->routes[$method][$uri] = $action;
    }

    public function dispatch(): void
    {
        $method = Request::method();
        $uri = Request::uri();

        $action = $this->routes[$method][$uri] ?? null;

        if ($action === null) {
            JsonResponse::error(
                'Route not found.',
                404
            );
        }

        if (is_callable($action)) {
            $action();
            return;
        }

        [$controller, $controllerMethod] = $action;

        $instance = new $controller();

        $instance->$controllerMethod();
    }
}