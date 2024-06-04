<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Domain\Validator;

use Romira\Zenita\Feature\Auth\Domain\Exception\InvalidAuthUserPasswordException;

class AuthUserPasswordValidator
{
    const int MIN_PASSWORD_LENGTH = 8;
    const int MAX_PASSWORD_LENGTH = 30;

    public static function validate(string $password): ?InvalidAuthUserPasswordException
    {
        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            return new InvalidAuthUserPasswordException('Password is too short');
        }

        if (strlen($password) > self::MAX_PASSWORD_LENGTH) {
            return new InvalidAuthUserPasswordException('Password is too long');
        }

        // 少なくとも1つの小文字、大文字、数字、特殊文字を含む
        if (!preg_match('/\A(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_-])[A-Za-z\d@$!%*?&_-]+\z/u', $password)) {
            return new InvalidAuthUserPasswordException('Password is invalid');
        }

        return null;
    }
}
