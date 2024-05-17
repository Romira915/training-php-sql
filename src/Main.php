<?php

declare(strict_types=1);

namespace Romira\Zenita;

use Exception;
use Monolog\Level;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Interfaces\Routes\Route;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\GetIndex;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\PostArticles;
use Romira\Zenita\Utils\Logger\LoggerFactory;


class Main
{
    public static function run(): void
    {
        $httpRequest = self::createHttpRequest();
        $route = new Route($httpRequest);

        $route->get('/', new GetIndex())->post('/articles', new PostArticles());

        try {
            $route->run();
        } catch (Exception $e) {
            $logger = LoggerFactory::createLogger('error', Level::Error);
            $logger->error($e->getFile() . ":" . $e->getLine() . " " . $e->getMessage(), ['exception' => $e]);

            http_response_code(500);
            echo 'Internal Server Error';
        }
    }

    private static function createHttpRequest(): HttpRequest
    {
        return new HttpRequest(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            $_GET,
            $_POST,
            $_SESSION ?? [],
            $_FILES,
            $_SERVER,
            $_COOKIE,
            $_ENV
        );
    }
}
