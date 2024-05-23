<?php

namespace Romira\Zenita\Feature\Article\Domain\Repositories;

use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidUploadImageException;

interface ImageStorageInterface
{
    /**
     * @return string The file name
     *
     * @throws InvalidUploadImageException
     */
    public function moveUploadedFile(string $tmpName): string;
}
