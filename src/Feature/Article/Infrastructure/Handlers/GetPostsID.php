<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Infrastructure\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;

class GetPostsID implements HandlerInterface
{
    public static function handle(HttpRequest $request, array $matches): HttpResponse
    {
        return new HttpResponse(statusCode: 200, body: 'GET /posts/{post_id} post_id = ' . $matches['post_id']);
    }
}
