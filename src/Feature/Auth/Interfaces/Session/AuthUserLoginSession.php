<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Interfaces\Session;

use Romira\Zenita\Common\Interfaces\Session\Session;

class AuthUserLoginSession
{
    const string AUTH_LOGIN_ERROR_MESSAGE_KEY = 'auth_login_error_message';

    public function __construct(private Session $session)
    {
    }

    public function setAuthLoginErrorMessage(string $message): void
    {
        $this->session->set(self::AUTH_LOGIN_ERROR_MESSAGE_KEY, $message);
    }

    public function flashAuthLoginErrorMessage(): ?string
    {
        return $this->session->flash(self::AUTH_LOGIN_ERROR_MESSAGE_KEY);
    }
}
