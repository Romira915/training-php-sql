<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Http\SeeOtherResponse;
use Romira\Zenita\Common\Interfaces\Handlers\SessionHandlerInterface;
use Romira\Zenita\Common\Interfaces\Session\CurrentUserSession;
use Romira\Zenita\Common\Interfaces\Session\Session;

class PostAuthLogout implements SessionHandlerInterface
{
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse
    {
        $currentUserSession = new CurrentUserSession($session);
        $currentUserSession->logout();

        return new SeeOtherResponse('/');
    }
}
