<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Application\DTO;

readonly class RegisterAuthUserDTO
{
    public function __construct(public string $displayName, public string $password, public ?string $icon_path)
    {
    }
}
