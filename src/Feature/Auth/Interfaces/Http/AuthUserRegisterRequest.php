<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Interfaces\Http;

use Romira\Zenita\Feature\Auth\Domain\Exception\InvalidAuthUserPasswordException;
use Romira\Zenita\Feature\Auth\Domain\Exception\InvalidUserDisplayNameException;
use Romira\Zenita\Feature\Auth\Domain\Validator\AuthUserPasswordValidator;
use Romira\Zenita\Feature\Auth\Domain\Validator\UserDisplayNameValidator;

readonly class AuthUserRegisterRequest
{
    private function __construct(public string $displayName, public string $password)
    {
    }

    public static function new(string $displayName, string $password): AuthUserRegisterRequest|InvalidUserDisplayNameException|InvalidAuthUserPasswordException
    {
        $invalidDisplayNameException = UserDisplayNameValidator::validate($displayName);
        if ($invalidDisplayNameException !== null) {
            return $invalidDisplayNameException;
        }

        $invalidPasswordException = AuthUserPasswordValidator::validate($password);
        if ($invalidPasswordException !== null) {
            return $invalidPasswordException;
        }

        return new AuthUserRegisterRequest($displayName, $password);
    }
}
