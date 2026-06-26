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

        if (!isset($this->routes[$method])) {

            http_response_code(405);
            echo "Método não permitido";

            return;
        }

        foreach ($this->routes[$method] as $routeUri => $route) {

            /*
            * Converte:
            * /usuarios/{id}
            *
            * em:
            * #^/usuarios/([^/]+)$#
            */
            $pattern = preg_replace(
                '/\{([a-zA-Z0-9_]+)\}/',
                '([^/]+)',
                $routeUri
            );

            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            array_shift($matches);

            foreach (
                $route['middlewares']
                as $middleware
            ) {
                $this->make($middleware)
                    ->handle();
            }

            [$controller, $action] =
                $route['action'];

            $this->make($controller)
                ->$action(...$matches);

            return;
        }

        http_response_code(404);

        echo "Página não encontrada";
    }

    private function make(string $class): object
    {
        $reflection = new ReflectionClass($class);

        $constructor = $reflection->getConstructor();

        // Sem construtor → instancia normalmente
        if ($constructor === null) {
            return new $class();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type === null) {
                throw new Exception("Não foi possível resolver parâmetro {$parameter->getName()} em {$class}");
            }

            if ($type instanceof \ReflectionUnionType) {
                throw new Exception("Tipos union não suportados ainda em {$class}");
            }

            $typeName = $type->getName();

            // ====================== CORREÇÃO PRINCIPAL ======================
            // Trata tipos primitivos (string, int, bool, float, array)
            if (in_array($typeName, ['string', 'int', 'float', 'bool', 'array'])) {
                throw new Exception(
                    "Parâmetro '{$parameter->getName()}' do tipo '{$typeName}' " .
                    "em {$class} não pode ser resolvido automaticamente. " .
                    "Use injeção de dependência ou passe valor manualmente."
                );
            }

            // PDO especial
            if ($typeName === \PDO::class) {
                $dependencies[] = Connection::getConnection();
                continue;
            }

            // Resolve classes normalmente (recursivo)
            $dependencies[] = $this->make($typeName);
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}
