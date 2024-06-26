<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Interfaces\Validator;

use Romira\Zenita\Common\Interfaces\Exception\InvalidUploadImageException;

class UploadedImageValidator
{
    const int MAX_FILE_SIZE = 1024 * 1024 * 10; // 10MiB
    const array ALLOWED_MIME_TYPES = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];

    /**
     * @param array $file
     * @return InvalidUploadImageException|null
     */
    public static function validate(array $file): InvalidUploadImageException|null
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return new InvalidUploadImageException('File upload error. upload error code: ' . $file['error']);
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            return new InvalidUploadImageException('File size too large');
        }

        if (!in_array(exif_imagetype($file['tmp_name']), self::ALLOWED_MIME_TYPES)) {
            return new InvalidUploadImageException('Invalid file type');
        }

        return null;
    }
}
