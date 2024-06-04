<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Interfaces\Handlers\SessionHandlerInterface;
use Romira\Zenita\Common\Interfaces\Session\Session;
use Romira\Zenita\Feature\Auth\Presentation\UserRegisterPageViewHelper;

class GetAuthRegister implements SessionHandlerInterface
{
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse
    {
        $viewHelper = new UserRegisterPageViewHelper();
        $html = $viewHelper->render();

        return new HttpResponse(statusCode: 200, body: $html);
    }
}
