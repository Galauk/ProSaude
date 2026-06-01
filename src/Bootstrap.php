<?php

declare(strict_types=1);

namespace App;

use App\Core\Config\AppConfig;

final class Bootstrap
{
    public static function init(): void
    {
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
        }

        AppConfig::load();
    }
}
