<?php
namespace App;

use App\Routing\Router;

class Application
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function run(): void
    {
        require __DIR__ . '/../config/routes.php';

        $this->router->dispatch();
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}