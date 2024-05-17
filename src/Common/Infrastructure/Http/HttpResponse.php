<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Infrastructure\Http;

readonly class HttpResponse
{
    public function __construct(private int $statusCode = 200, private array $headers = [], private string $body = '')
    {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function emit(): void
    {
        http_response_code($this->getStatusCode());

        foreach ($this->getHeaders() as $header => $value) {
            // header injection 対策
            $header = str_replace(["\r", "\n"], '', $header);
            $value = str_replace(["\r", "\n"], '', $value);

            header($header . ': ' . $value);
        }

        echo $this->getBody();
    }
}
