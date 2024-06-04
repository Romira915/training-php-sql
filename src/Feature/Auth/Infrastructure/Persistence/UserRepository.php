<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Infrastructure\Persistence;

use PDO;
use Romira\Zenita\Feature\Auth\Domain\Entities\AuthUser;

class UserRepository
{
    public static function create(PDO $pdo, AuthUser $user): AuthUser
    {
        $statement = $pdo->prepare('
            INSERT INTO users DEFAULT VALUES RETURNING id
        ');
        $statement->execute();
        $user->setId((int)$statement->fetchColumn());

        $statement = $pdo->prepare('
            INSERT INTO user_detail (user_id, display_name, icon_path) VALUES (:user_id, :display_name, :icon_path)
        ');
        $statement->execute([
            'user_id' => $user->getId(),
            'display_name' => $user->getDisplayName(),
            'icon_path' => $user->getIconPath(),
        ]);

        $statement = $pdo->prepare('
            INSERT INTO user_hashed_password (user_id, hashed_password) VALUES (:user_id, :hashed_password)
        ');
        $statement->execute([
            'user_id' => $user->getId(),
            'hashed_password' => $user->getHashedPassword(),
        ]);

        return $user;
    }

    public static function findById(PDO $pdo, int $id): ?AuthUser
    {
        $statement = $pdo->prepare('
            SELECT
                u.id,
                ud.display_name,
                ud.icon_path,
                uhp.hashed_password
            FROM
                users u
                JOIN user_detail ud ON u.id = ud.user_id
                JOIN user_hashed_password uhp ON u.id = uhp.user_id
            WHERE
                u.id = :id
        ');
        $statement->execute([
            'id' => $id,
        ]);

        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }

        return new AuthUser(
            display_name: $row['display_name'],
            hashed_password: $row['hashed_password'],
            icon_path: $row['icon_path'],
            id: (int)$row['id'],
        );
    }

    public static function findByDisplayName(PDO $pdo, string $display_name): ?AuthUser
    {
        $statement = $pdo->prepare('
            SELECT
                u.id,
                ud.display_name,
                ud.icon_path,
                uhp.hashed_password
            FROM
                users u
                JOIN user_detail ud ON u.id = ud.user_id
                JOIN user_hashed_password uhp ON u.id = uhp.user_id
            WHERE
                ud.display_name = :display_name
        ');
        $statement->execute([
            'display_name' => $display_name,
        ]);

        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }

        return new AuthUser(
            display_name: $row['display_name'],
            hashed_password: $row['hashed_password'],
            icon_path: $row['icon_path'],
            id: (int)$row['id'],
        );
    }
}
