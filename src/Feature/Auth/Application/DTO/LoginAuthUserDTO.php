<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Application\DTO;

class LoginAuthUserDTO
{
    public function __construct(
        public string $displayName,
        public string $password
    )
    {
    }
}
