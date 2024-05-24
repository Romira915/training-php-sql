<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;

class GetArticlesId implements HandlerInterface
{
    public static function handle(HttpRequest $request, array $matches): HttpResponse
    {
        // TODO: Implement handle() method.
        return new HttpResponse(statusCode: 200, body: 'GetArticlesId. id: ' . $matches['id'] . ' is not implemented yet');
    }
}
