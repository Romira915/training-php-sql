<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\User\Domain\Entities;

class User
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
