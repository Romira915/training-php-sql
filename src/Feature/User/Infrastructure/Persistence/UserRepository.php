<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\User\Infrastructure\Persistence;

use PDO;
use Romira\Zenita\Feature\User\Domain\Entities\User;

class UserRepository
{
    public static function create(PDO $pdo, User $user): void
    {
        $statement = $pdo->prepare('
            INSERT INTO users DEFAULT VALUES RETURNING id
        ');
        $statement->execute();
        $user->setId((int)$statement->fetchColumn());
    }
}
