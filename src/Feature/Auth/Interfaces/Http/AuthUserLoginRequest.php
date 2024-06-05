<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Interfaces\Http;

readonly class AuthUserLoginRequest
{
    private function __construct(public string $displayName, public string $password)
    {
    }

    public static function new(string $displayName, string $password): AuthUserLoginRequest
    {
        return new AuthUserLoginRequest($displayName, $password);
    }
}
