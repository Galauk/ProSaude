<?php

declare(strict_types=1);

namespace App\Shared\Support;

final class ArrayHelper
{
    public static function get(array $data, string $key, mixed $default = null): mixed
    {
        return $data[$key] ?? $default;
    }
}
