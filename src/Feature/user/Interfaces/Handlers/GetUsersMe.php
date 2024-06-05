<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\user\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Infrastructure\QueryServices\CurrentUserServiceQuery;
use Romira\Zenita\Common\Interfaces\Handlers\SessionHandlerInterface;
use Romira\Zenita\Common\Interfaces\Session\CurrentUserSession;
use Romira\Zenita\Common\Interfaces\Session\Session;

class GetUsersMe implements SessionHandlerInterface
{
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse
    {
        $currentUserSession = new CurrentUserSession($session);
        if (!$currentUserSession->isLoggedIn()) {
            return new HttpResponse(200, ['Content-Type' => 'application/json'], json_encode([
                'message' => 'Unauthorized',
            ]));
        }

        $pdo = PostgresqlConnection::connect();
        $currentUser = CurrentUserServiceQuery::getCurrentUserById($pdo, $currentUserSession->getCurrentUser() ?? 0);

        return new HttpResponse(200, ['Content-Type' => 'application/json', 'Cache-Control' => 'no-store'], json_encode([
            'display_name' => $currentUser->display_name,
            'icon_path' => $currentUser->icon_path,
            'message' => 'OK',
        ]));
    }
}
