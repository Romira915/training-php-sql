<?php

declare(strict_types=1);

namespace Romira\Zenita\Config;

class Config
{
    const string IMAGE_PATH_PREFIX = '/images/';

    public static function getDatabaseConfig(): array
    {
        return [
            'host' => getenv('POSTGRES_HOST') ? getenv('POSTGRES_HOST') : 'localhost',
            'port' => getenv('POSTGRES_PORT') ? getenv('POSTGRES_PORT') : '5432',
            'dbname' => getenv('POSTGRES_DB') ? getenv('POSTGRES_DB') : 'app',
            'user' => getenv('POSTGRES_USER') ? getenv('POSTGRES_USER') : 'app',
            'password' => getenv('POSTGRES_PASSWORD') ? getenv('POSTGRES_PASSWORD') : 'password',
        ];
    }

    public static function getImageBaseUrl(): string
    {
        return getenv('IMAGE_BASE_URL') ? getenv('IMAGE_BASE_URL') : 'http://localhost:8080';
    }
}
