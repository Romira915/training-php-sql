<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Interfaces\Handlers;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Interfaces\Session\Session;

interface HandlerInterface
{
    /**
     * @param HttpRequest $request
     * @param array $matches
     * @param Session|null $session
     * @return HttpResponse
     */
    public static function handle(HttpRequest $request, array $matches, ?Session &$session = null): HttpResponse;
}
