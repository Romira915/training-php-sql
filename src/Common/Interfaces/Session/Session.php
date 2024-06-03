<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Interfaces\Session;

class Session
{
    public function __construct()
    {
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function start(): void
    {
        session_start();
    }
}
