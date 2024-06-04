<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Domain\Validator;

use Romira\Zenita\Feature\Auth\Domain\Exception\InvalidUserDisplayNameException;

class UserDisplayNameValidator
{
    const int MAX_DISPLAY_NAME_LENGTH = 100;

    /**
     * @param string $displayName
     * @return InvalidUserDisplayNameException|null
     */
    public static function validate(string $displayName): InvalidUserDisplayNameException|null
    {
        $pattern = '/\A[a-zA-Z0-9_-]+\z/u';

        if (mb_strlen($displayName) > self::MAX_DISPLAY_NAME_LENGTH) {
            return new InvalidUserDisplayNameException('Display name is too long');
        }

        if (!preg_match($pattern, $displayName)) {
            return new InvalidUserDisplayNameException('Display name contains invalid characters');
        }

        return null;
    }
}
