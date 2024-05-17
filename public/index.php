<?php

use Monolog\Level;
use Romira\Zenita\Main;
use Romira\Zenita\Utils\Logger\LoggerFactory;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    Main::run();
} catch (Exception $e) {
    $logger = LoggerFactory::createLogger('error', Level::Error);
    $logger->error($e->getFile() . ":" . $e->getLine() . " " . $e->getMessage(), ['exception' => $e]);

    http_response_code(500);
    echo 'Internal Server Error';
}
