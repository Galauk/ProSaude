<?php

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return __DIR__ . '/' . ltrim($path, '/');
    }
}

if (!function_exists('database_path')) {
    function database_path(string $path = ''): string
    {
        return base_path('database/' . ltrim($path, '/'));
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        // Ajuste conforme sua configuração
        return rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {

    function asset(string $path): string
    {
        return rtrim(
            $_ENV['APP_URL'],
            '/'
        ) . '/public/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {

    function url(string $path = ''): string
    {
        return rtrim(
            $_ENV['APP_URL'],
            '/'
        ) . '/' . ltrim($path, '/');
    }
}

function redirect(
    string $url
): never {

    header(
        'Location: '
        . base_url($url)
    );

    exit;
}

function is_logged(): bool
{
    return isset(
        $_SESSION['usuario']
    );
}