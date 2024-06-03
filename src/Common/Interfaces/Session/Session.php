<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Interfaces\Session;

class Session
{
    public function __construct(private array $session = [])
    {
    }

    public function get(string $key): mixed
    {
        return $this->session[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->session[$key] = $value;
    }

    public function all(): array
    {
        return $this->session;
    }
}
