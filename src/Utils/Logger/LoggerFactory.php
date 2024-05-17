<?php

declare(strict_types=1);

namespace Romira\Zenita\Utils\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class LoggerFactory
{
    private const string LOG_PATH = __DIR__ . '/../../../logs/';

    public static function createLogger(string $channel, Level $logLevel): Logger
    {
        $logger = new Logger($channel);
        $logger->pushHandler(new StreamHandler(self::LOG_PATH . $channel . '.log', $logLevel));

        return $logger;
    }
}
