<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use Exception;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;

class PostArticles implements HandlerInterface
{
    /**
     * @throws Exception
     */
    public static function handle(HttpRequest $request, array $matches): HttpResponse
    {
        return new HttpResponse(statusCode: 302, headers: ['location' => '/']);
    }
}
