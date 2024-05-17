<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;

interface HandlerInterface
{
    /**
     * @param HttpRequest $request
     * @param array $matches
     * @return HttpResponse
     */
    public static function handle(HttpRequest $request, array $matches): HttpResponse;
}
