<?php
namespace App\Routing;

class Router
{
    private array $routes = [];

    public function get(
        string $uri,
        array $action,
        array $middlewares = []
    ): void {

        $this->routes['GET'][$uri] = [
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public function post(
        string $uri,
        array $action,
        array $middlewares = []
    ): void {

        $this->routes['POST'][$uri] = [
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $uri = parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );

        if (!isset($this->routes[$method][$uri])) {

            http_response_code(404);

            echo "Página não encontrada";

            return;
        }

        $route = $this->routes[$method][$uri];

        foreach (
            $route['middlewares']
            as $middleware
        ) {

            (new $middleware())->handle();
        }

        [$controller, $action] =
            $route['action'];

        (new $controller())->$action();
    }
}