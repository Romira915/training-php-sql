<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Application\UseCases;

use Exception;
use PDO;
use Romira\Zenita\Common\Interfaces\Session\CurrentUserSession;
use Romira\Zenita\Feature\Auth\Application\DTO\RegisterAuthUserDTO;
use Romira\Zenita\Feature\Auth\Application\Exception\UsernameAlreadyExistsException;
use Romira\Zenita\Feature\Auth\Domain\Entities\AuthUser;
use Romira\Zenita\Feature\Auth\Domain\Repositories\UserIconImageStorageInterface;
use Romira\Zenita\Feature\Auth\Infrastructure\Persistence\AuthUserRepository;

class RegisterAuthUserUseCase
{
    /**
     * @throws UsernameAlreadyExistsException
     */
    public static function run(PDO $pdo, AuthUserRepository $authUserRepository, UserIconImageStorageInterface $iconImageStorage, CurrentUserSession $currentUserSession, RegisterAuthUserDTO $authUserDTO): void
    {
        $pdo->beginTransaction();
        try {
            $authUser = $authUserRepository->findByDisplayName($pdo, $authUserDTO->displayName);
            if ($authUser !== null) {
                throw new UsernameAlreadyExistsException();
            }

            if ($authUserDTO->icon_path !== null) {
                $icon_path = $iconImageStorage->moveUploadedFile($authUserDTO->icon_path, $authUserDTO->displayName);
            } else {
                $icon_path = $iconImageStorage->getDefaultIconPath();
            }

            $hashed_password = password_hash($authUserDTO->password, PASSWORD_DEFAULT);
            $newAuthUser = new AuthUser(display_name: $authUserDTO->displayName, hashed_password: $hashed_password, icon_path: $icon_path);

            $newAuthUser = $authUserRepository->create($pdo, $newAuthUser);
            $pdo->commit();

            $currentUserSession->setCurrentUser($newAuthUser->getId());
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
