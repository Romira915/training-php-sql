<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Application\UseCases;

use PDO;
use Romira\Zenita\Common\Interfaces\Session\CurrentUserSession;
use Romira\Zenita\Feature\Auth\Application\DTO\LoginAuthUserDTO;
use Romira\Zenita\Feature\Auth\Application\Exception\PasswordVerificationFailedException;
use Romira\Zenita\Feature\Auth\Application\Exception\UserNotFoundException;
use Romira\Zenita\Feature\Auth\Infrastructure\Persistence\AuthUserRepository;

class LoginAuthUserUseCase
{
    public static function run(PDO $pdo, AuthUserRepository $authUserRepository, CurrentUserSession $currentUserSession, LoginAuthUserDTO $authUserDTO): void
    {
        $authUser = $authUserRepository->findByDisplayName($pdo, $authUserDTO->displayName);
        if ($authUser === null) {
            throw new UserNotFoundException('User not found: display_name=' . $authUserDTO->displayName);
        }

        if (!password_verify($authUserDTO->password, $authUser->getHashedPassword())) {
            throw new PasswordVerificationFailedException('Password does not match');
        }

        $currentUserSession->setCurrentUser($authUser->getId());
    }
}
