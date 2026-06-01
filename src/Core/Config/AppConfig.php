<?php

declare(strict_types=1);

namespace App\Core\Config;

final class AppConfig
{
    private const CONFIG = [
        'app_name' => 'SocialSaude',
        'env' => 'local',
        'timezone' => 'America/Sao_Paulo',
    ];

    public static function load(): void
    {
        date_default_timezone_set(self::CONFIG['timezone']);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::CONFIG[$key] ?? $default;
    }
}
