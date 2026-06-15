<?php
namespace App\Core;

Use App\Core\Environment;
use App\Routing\Router;

class Application
{
    private Router $router;

    public function __construct()
    {
        $this->loadEnvironment();
        $this->router = new Router();
    }

    private function loadEnvironment(): void
    {
        Environment::load(
            dirname(__DIR__) . '/../.env'
        );
    }

    public function run(): void
    {
        $router = $this->router;

        require dirname(__DIR__, 2) . '/config/routes.php';

        $this->router->dispatch();
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}