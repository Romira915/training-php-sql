<?php

namespace Romira\Zenita;

use Romira\Zenita\Common\Interfaces\Routes\Route;
use Romira\Zenita\Feature\Article\Infrastructure\Handlers\GetIndex;
use Romira\Zenita\Feature\Article\Infrastructure\Handlers\GetPostsID;


class Main
{
    public static function run(): void
    {
        $route = new Route();

        $route->get('/', new GetIndex())->get('/posts/{post_id}', new GetPostsID());

        $route->run();
    }
}
