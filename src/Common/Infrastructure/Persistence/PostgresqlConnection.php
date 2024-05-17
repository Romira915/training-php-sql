<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Infrastructure\Persistence;

use PDO;
use Romira\Zenita\Config\Config;

class PostgresqlConnection
{
    public static function connect(): PDO
    {
        $db_config = Config::getDatabaseConfig();

        return new PDO(
            sprintf(
                'pgsql:host=%s;port = %s;dbname=%s;user=%s;password=%s',
                $db_config['host'],
                $db_config['port'],
                $db_config['dbname'],
                $db_config['user'],
                $db_config['password']
            ));
    }
}
