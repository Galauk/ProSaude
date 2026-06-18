<?php

namespace App\Controllers;

use App\Core\View;

abstract class BaseController
{
    protected function render(
        string $view,
        array $data = [],
        string $layout = 'public'
    ): void {
        View::render($view, $data, $layout);
    }

    protected function redirect(
        string $url
    ): void {
        header("Location: {$url}");
        exit;
    }
}
