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
use Romira\Zenita\Feature\Article\Application\Exception\UsernameAlreadyExistsException;
use Romira\Zenita\Feature\Auth\Application\DTO\RegisterAuthUserDTO;
use Romira\Zenita\Feature\Auth\Application\UseCases\RegisterAuthUserUseCase;
use Romira\Zenita\Feature\Auth\Domain\Exception\InvalidAuthUserPasswordException;
use Romira\Zenita\Feature\Auth\Domain\Exception\InvalidUserDisplayNameException;
use Romira\Zenita\Feature\Auth\Infrastructure\FileStorage\UserIconImageLocalStorage;
use Romira\Zenita\Feature\Auth\Infrastructure\Persistence\AuthUserRepository;
use Romira\Zenita\Feature\Auth\Interfaces\Http\AuthUserRegisterRequest;
use Romira\Zenita\Feature\Auth\Interfaces\Session\AuthUserRegisterSession;

class PostAuthRegister implements SessionHandlerInterface
{
    public static function handle(HttpRequest $request, array $matches, Session &$session): HttpResponse
    {
        $authUserRegisterRequest = AuthUserRegisterRequest::new(
            $request->post['user_name'],
            $request->post['password'],
            $request->files['user_icon']
        );
        $authUserSession = new AuthUserRegisterSession($session);

        if ($authUserRegisterRequest instanceof InvalidUserDisplayNameException) {
            $authUserSession->setAuthRegisterErrorMessage('※ユーザー名は最大100文字で、英数字、アンダースコア(_)、およびハイフン(-)のみを使用してください。');
            return new SeeOtherResponse('/auth/register');
        }
        if ($authUserRegisterRequest instanceof InvalidAuthUserPasswordException) {
            $authUserSession->setAuthRegisterErrorMessage('※パスワードは8文字以上30文字以内で、大文字、小文字、数字、記号をそれぞれ1文字以上含めてください。記号は@$!%*?&_-が使用可能です。');
            return new SeeOtherResponse('/auth/register');
        }

        $pdo = PostgresqlConnection::connect();
        $authUserRepository = new AuthUserRepository();
        $userIconImageStorage = new UserIconImageLocalStorage($request->server['DOCUMENT_ROOT']);
        $currentUserSession = new CurrentUserSession($session);
        $authUserDTO = new RegisterAuthUserDTO(
            $authUserRegisterRequest->displayName,
            $authUserRegisterRequest->password,
            $authUserRegisterRequest->icon_tmp_path
        );

        try {
            RegisterAuthUserUseCase::run($pdo, $authUserRepository, $userIconImageStorage, $currentUserSession, $authUserDTO);
        } catch (UsernameAlreadyExistsException) {
            $authUserSession->setAuthRegisterErrorMessage('※そのユーザー名は使用できません。');
            return new SeeOtherResponse('/auth/register');
        }

        return new SeeOtherResponse('/');
    }
}
