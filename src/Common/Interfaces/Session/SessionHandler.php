<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Interfaces\Session;

class SessionHandler
{
    public static function start(): void
    {
        session_start();
    }

    public static function setAll(array $session): void
    {
        $_SESSION = $session;
    }

    public static function getAll(): array
    {
        return $_SESSION ?? [];
    }
}
