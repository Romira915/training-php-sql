<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Domain\Repositories;

interface UserIconImageStorageInterface
{
    public function moveUploadedFile(string $tmpName, string $displayName): string;

    public function copyDefaultIcon(string $displayName): string;
}
