<?php

declare(strict_types=1);

namespace Romira\Zenita;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Interfaces\Routes\Route;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\GetIndex;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\GetUsersIdArticlesId;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\GetUsersIdArticlesIdEdit;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\PostUsersIdArticleIdDelete;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\PostUsersIdArticleIdEdit;
use Romira\Zenita\Feature\Article\Interfaces\Handlers\PostUsersIdArticles;
use Romira\Zenita\Feature\Auth\Interfaces\Handlers\GetAuthLogin;
use Romira\Zenita\Feature\Auth\Interfaces\Handlers\GetAuthRegister;
use Romira\Zenita\Feature\Auth\Interfaces\Handlers\PostAuthLogin;
use Romira\Zenita\Feature\Auth\Interfaces\Handlers\PostAuthLogout;
use Romira\Zenita\Feature\Auth\Interfaces\Handlers\PostAuthRegister;


class Main
{
    /**
     * @throws \Exception
     */
    public static function run(): void
    {
        $httpRequest = self::createHttpRequest();
        $route = new Route($httpRequest);

        $route
            ->get('/', new GetIndex())
            ->post('/users/{user_id}/articles', new PostUsersIdArticles())
            ->get('/users/{user_id}/articles/{article_id}', new GetUsersIdArticlesId())
            ->get('/users/{user_id}/articles/{article_id}/edit', new GetUsersIdArticlesIdEdit())
            ->post('/users/{user_id}/articles/{article_id}/edit', new PostUsersIdArticleIdEdit())
            ->post('/users/{user_id}/articles/{article_id}/delete', new PostUsersIdArticleIdDelete())
            ->get('/auth/login', new GetAuthLogin())
            ->post('/auth/login', new PostAuthLogin())
            ->get('/auth/register', new GetAuthRegister())
            ->post('/auth/register', new PostAuthRegister())
            ->post('/auth/logout', new PostAuthLogout());

        $route->run();
    }

    private static function createHttpRequest(): HttpRequest
    {
        return new HttpRequest(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            $_GET,
            $_POST,
            $_FILES,
            $_SERVER,
            $_COOKIE,
            $_ENV
        );
    }
}
