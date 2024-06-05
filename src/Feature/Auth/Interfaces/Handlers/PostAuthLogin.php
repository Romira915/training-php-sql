<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Infrastructure\Http\SeeOtherResponse;
use Romira\Zenita\Common\Infrastructure\Persistence\PostgresqlConnection;
use Romira\Zenita\Common\Interfaces\Handlers\SessionHandlerInterface;
use Romira\Zenita\Common\Interfaces\Session\CurrentUserSession;
use Romira\Zenita\Common\Interfaces\Session\Session;
use Romira\Zenita\Feature\Auth\Application\DTO\LoginAuthUserDTO;
use Romira\Zenita\Feature\Auth\Application\Exception\PasswordVerificationFailedException;
use Romira\Zenita\Feature\Auth\Application\Exception\UserNotFoundException;
use Romira\Zenita\Feature\Auth\Application\UseCases\LoginAuthUserUseCase;
use Romira\Zenita\Feature\Auth\Infrastructure\Persistence\AuthUserRepository;
use Romira\Zenita\Feature\Auth\Interfaces\Http\AuthUserLoginRequest;
use Romira\Zenita\Feature\Auth\Interfaces\Session\AuthUserLoginSession;

class PostAuthLogin implements SessionHandlerInterface
{
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse
    {
        $authUserLoginRequest = AuthUserLoginRequest::new(
            $request->post['user_name'],
            $request->post['password']
        );

        $pdo = PostgresqlConnection::connect();
        $authUserRepository = new AuthUserRepository();
        $currentUserSession = new CurrentUserSession($session);
        $loginAuthUserDTO = new LoginAuthUserDTO($authUserLoginRequest->displayName, $authUserLoginRequest->password);

        try {
            LoginAuthUserUseCase::run($pdo, $authUserRepository, $currentUserSession, $loginAuthUserDTO);
        } catch (PasswordVerificationFailedException|UserNotFoundException) {
            $authLoginSession = new AuthUserLoginSession($session);
            $authLoginSession->setAuthLoginErrorMessage('Invalid user name or password');
            return new SeeOtherResponse('/auth/login');
        }

        return new SeeOtherResponse('/');
    }
}
