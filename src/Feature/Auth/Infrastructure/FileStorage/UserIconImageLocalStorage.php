<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Infrastructure\FileStorage;

use Romira\Zenita\Common\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Feature\Auth\Domain\Repositories\UserIconImageStorageInterface;
use Romira\Zenita\Utils\File;

class UserIconImageLocalStorage implements UserIconImageStorageInterface
{
    const string DEFAULT_ICON_PATH = __DIR__ . '/../../../../../assets/default_user_icon.png';
    const string USER_ICON_PATH_PREFIX = '/users/icons/';

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

    /**
     * @throws InvalidUploadImageException
     */
    public function copyDefaultIcon(string $displayName): string
    {
        if (!File::copy(self::DEFAULT_ICON_PATH, $this->rootDir . self::USER_ICON_PATH_PREFIX . $displayName)) {
            throw new InvalidUploadImageException('Failed to copy default icon');
        }

        return self::USER_ICON_PATH_PREFIX . $displayName;
    }
}
