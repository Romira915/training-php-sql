<?php

declare(strict_types=1);

namespace Romira\Zenita;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Interfaces\Routes\Route;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\GetIndex;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\PostArticles;


class Main
{
    public static function run(): void
    {
        $httpRequest = self::createHttpRequest();
        $route = new Route($httpRequest);

        $route->get('/', new GetIndex())->post('/articles', new PostArticles());

        $route->run();
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
