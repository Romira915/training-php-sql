<?php

declare(strict_types=1);

namespace Romira\Zenita\Utils\Random;

class Random
{
    public static function bytes(int $length): string
    {
        return random_bytes($length);
    }

    public static function string(int $length): string
    {
        return bin2hex(self::bytes($length));
    }
}
