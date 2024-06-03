<?php

namespace Romira\Zenita\Common\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Interfaces\Session\Session;

interface SessionHandlerInterface
{
    /**
     * @param HttpRequest $request
     * @param array $matches
     * @param Session $session
     * @return HttpResponse
     */
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse;
}
