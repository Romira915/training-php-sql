<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Interfaces\Http;

use Romira\Zenita\Common\Interfaces\Exception\InvalidUploadImageException;
use Romira\Zenita\Feature\Auth\Domain\Exception\InvalidAuthUserPasswordException;
use Romira\Zenita\Feature\Auth\Domain\Exception\InvalidUserDisplayNameException;
use Romira\Zenita\Feature\Auth\Domain\Validator\AuthUserPasswordValidator;
use Romira\Zenita\Feature\Auth\Domain\Validator\UserDisplayNameValidator;
use Romira\Zenita\Feature\Auth\Interfaces\Validator\UploadedImageValidator;

readonly class AuthUserRegisterRequest
{
    private function __construct(public string $displayName, public string $password, public ?string $icon_tmp_path)
    {
    }

    public static function new(string $displayName, string $password, array $iconFile): AuthUserRegisterRequest|InvalidUserDisplayNameException|InvalidAuthUserPasswordException|InvalidUploadImageException
    {
        $invalidDisplayNameException = UserDisplayNameValidator::validate($displayName);
        if ($invalidDisplayNameException !== null) {
            return $invalidDisplayNameException;
        }

        $invalidPasswordException = AuthUserPasswordValidator::validate($password);
        if ($invalidPasswordException !== null) {
            return $invalidPasswordException;
        }

        $icon_tmp_path = null;
        if ($iconFile['tmp_name'] !== '') {
            $invalidUploadImageException = UploadedImageValidator::validate($iconFile);
            if ($invalidUploadImageException !== null) {
                return $invalidUploadImageException;
            }

            $icon_tmp_path = $iconFile['tmp_name'];
        }

        return new AuthUserRegisterRequest($displayName, $password, $icon_tmp_path);
    }
}
