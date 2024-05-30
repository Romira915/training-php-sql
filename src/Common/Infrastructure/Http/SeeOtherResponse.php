<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Infrastructure\Http;

readonly class SeeOtherResponse extends HttpResponse
{
    public function __construct(string $location, array $headers = [])
    {
        $headers['Location'] = $location;
        parent::__construct(statusCode: 303, headers: $headers);
    }
}
