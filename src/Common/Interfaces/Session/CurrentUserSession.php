<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Interfaces\Session;

class CurrentUserSession
{
    const string CURRENT_USER_ID_KEY = 'current_user_id';

    public function __construct(private Session $session)
    {
    }

    public function isLoggedIn(): bool
    {
        return $this->session->get(self::CURRENT_USER_ID_KEY) !== null;
    }

    public function setCurrentUser(int $user_id): void
    {
        $this->session->set(self::CURRENT_USER_ID_KEY, $user_id);
    }

    public function getCurrentUser(): ?int
    {
        return $this->session->get(self::CURRENT_USER_ID_KEY);
    }
}
