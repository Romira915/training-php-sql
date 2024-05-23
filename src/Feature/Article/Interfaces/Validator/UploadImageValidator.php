<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Validator;

use Romira\Zenita\Feature\Article\Interfaces\Exception\InvalidUploadImageException;

class UploadImageValidator
{
    const int MAX_FILE_SIZE = 1024 * 1024 * 50; // 50MiB
    const array ALLOWED_MIME_TYPES = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];

    /**
     * @throws InvalidUploadImageException
     */
    public static function validate(array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidUploadImageException('File upload error');
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new InvalidUploadImageException('File size too large');
        }

        if (!in_array(exif_imagetype($file['tmp_name']), self::ALLOWED_MIME_TYPES)) {
            throw new InvalidUploadImageException('Invalid file type');
        }
    }
}
