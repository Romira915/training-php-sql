<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Http\SeeOtherResponse;
use Romira\Zenita\Common\Interfaces\Handlers\SessionHandlerInterface;
use Romira\Zenita\Common\Interfaces\Session\CurrentUserSession;
use Romira\Zenita\Common\Interfaces\Session\Session;
use Romira\Zenita\Feature\Auth\Interfaces\Session\AuthUserRegisterSession;
use Romira\Zenita\Feature\Auth\Presentation\UserRegisterPageViewHelper;

class GetAuthRegister implements SessionHandlerInterface
{
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse
    {
        $currentUserSession = new CurrentUserSession($session);
        if ($currentUserSession->isLoggedIn()) {
            return new SeeOtherResponse('/');
        }

        $authUserSession = new AuthUserRegisterSession($session);

        $viewHelper = new UserRegisterPageViewHelper($authUserSession->flashAuthRegisterErrorMessage());
        $html = $viewHelper->render();

        return new HttpResponse(statusCode: 200, body: $html);
    }
}
