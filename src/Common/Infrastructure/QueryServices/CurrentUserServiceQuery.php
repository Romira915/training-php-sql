<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Infrastructure\QueryServices;

use PDO;
use Romira\Zenita\Common\Application\DTO\CurrentUserDTO;

class CurrentUserServiceQuery
{
    public static function getCurrentUserById(PDO $pdo, int $user_id): CurrentUserDTO|null
    {
        $statement = $pdo->prepare('
            SELECT id, display_name, icon_path
            FROM users
                INNER JOIN user_detail AS ud ON users.id = ud.user_id
            WHERE id = :user_id
        ');
        $statement->execute([
            'user_id' => $user_id,
        ]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new CurrentUserDTO(
            id: $row['id'],
            display_name: $row['display_name'],
            icon_path: $row['icon_path'],
        );
    }
}
