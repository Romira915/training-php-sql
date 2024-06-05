<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Infrastructure\FileStorage;

use Romira\Zenita\Common\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Feature\Auth\Domain\Repositories\UserIconImageStorageInterface;
use Romira\Zenita\Utils\File;

class UserIconImageLocalStorage implements UserIconImageStorageInterface
{
    const string USER_ICON_PATH_PREFIX = '/users/icons/';
    const string DEFAULT_ICON_PATH = self::USER_ICON_PATH_PREFIX . 'default_user_icon.png';

    public function __construct(
        private readonly string $rootDir
    )
    {
    }

    /**
     * @return string file path
     * @throws InvalidUploadImageException
     */
    public function moveUploadedFile(string $tmpName, string $displayName): string
    {
        $fileName = $displayName;
        $dir = $this->rootDir . self::USER_ICON_PATH_PREFIX;

        if (!File::moveUploadedFile($tmpName, $dir . $fileName)) {
            throw new InvalidUploadImageException('Failed to move uploaded file');
        }

        return $dir . $fileName;
    }

    public function getDefaultIconPath(): string
    {
        return self::DEFAULT_ICON_PATH;
    }
}
