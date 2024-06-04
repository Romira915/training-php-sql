<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Domain\Entities;

use Romira\Zenita\Feature\Auth\Domain\Exception\InvalidUserDisplayNameException;
use Romira\Zenita\Feature\Auth\Domain\Validator\UserDisplayNameValidator;

class AuthUser
{

    public function __construct(
        private string $display_name,
        private string $hashed_password,
        private string $icon_path,
        private ?int   $id = null
    )
    {
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    /**
     * @throws InvalidUserDisplayNameException
     */
    public function setDisplayName(string $display_name): void
    {
        $exception = UserDisplayNameValidator::validate($display_name);

        if ($exception !== null) {
            throw $exception;
        }

        $this->display_name = $display_name;
    }

    public function getDisplayName(): string
    {
        return $this->display_name;
    }

    public function getHashedPassword(): string
    {
        return $this->hashed_password;
    }

    public function getIconPath(): string
    {
        return $this->icon_path;
    }
}
