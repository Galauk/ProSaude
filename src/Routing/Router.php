<?php
namespace App\Routing;

use App\Core\Connection;

use ReflectionClass;
use Exception;

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

            $this->make($middleware)->handle();
        }

        [$controller, $action] =
            $route['action'];

        $this->make($controller)->$action();
    }

private function make(string $class): object
{
    $reflection = new ReflectionClass($class);

    $constructor = $reflection->getConstructor();

    /*
     * Não possui construtor
     */
    if ($constructor === null) {

        return new $class();
    }

    $dependencies = [];

    foreach (
        $constructor->getParameters()
        as $parameter
    ) {

        $type = $parameter->getType();

        if ($type === null) {

            throw new Exception(
                sprintf(
                    'Não foi possível resolver %s::%s',
                    $class,
                    $parameter->getName()
                )
            );
        }

        $dependency = $type->getName();

        /*
         * PDO é um caso especial
         */
        if ($dependency === PDO::class) {

            $dependencies[] =
                Connection::getConnection();

            continue;
        }

        /*
         * Resolve recursivamente
         */
        echo "Classe: {$class} -> Dependência: {$dependency}\n";
        $dependencies[] =
            $this->make($dependency);
    }

    return $reflection
        ->newInstanceArgs(
            $dependencies
        );
}}
