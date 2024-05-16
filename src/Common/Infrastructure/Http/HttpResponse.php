<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Infrastructure\Http;

class HttpResponse
{
    private int $statusCode;
    private array $headers = [];
    private string $body = '';

    public function __construct(int $statusCode = 200, array $headers = [], string $body = '')
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
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

    public function emit()
    {
        http_response_code($this->getStatusCode());

        foreach ($this->getHeaders() as $header => $value) {
            $header = str_replace(["\r", "\n"], '', $header);
            $value = str_replace(["\r", "\n"], '', $value);

            header($header . ': ' . $value);
        }

        echo $this->getBody();
    }
}
