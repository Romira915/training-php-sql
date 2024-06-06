<?php

declare(strict_types=1);

namespace Romira\Zenita\Feature\Article\Interfaces\Handlers\Session;

use Romira\Zenita\Common\Interfaces\Session\Session;

class TopPageErrorSession
{
    const string TOP_PAGE_ERROR_MESSAGE_KEY = 'top_page_error_message';

    public function __construct(private Session $session)
    {
    }

    public function setTopPageErrorMessage(string $message): void
    {
        $this->session->set(self::TOP_PAGE_ERROR_MESSAGE_KEY, $message);
    }

    public function flashTopPageErrorMessage(): ?string
    {
        return $this->session->flash(self::TOP_PAGE_ERROR_MESSAGE_KEY);
    }
}
