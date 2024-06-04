<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Auth\Interfaces\Session;

use Romira\Zenita\Common\Interfaces\Session\Session;

class AuthUserRegisterSession
{
    const string AUTH_REGISTER_ERROR_MESSAGE_KEY = 'auth_register_error_message';

    public function __construct(private Session $session)
    {
    }

    public function setAuthRegisterErrorMessage(string $message): void
    {
        $this->session->set(self::AUTH_REGISTER_ERROR_MESSAGE_KEY, $message);
    }

    public function flashAuthRegisterErrorMessage(): ?string
    {
        return $this->session->flash(self::AUTH_REGISTER_ERROR_MESSAGE_KEY);
    }
}
