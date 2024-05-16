<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Infrastructure\Http;

class HttpRequest
{
    public string $method;
    public string $uri;
    public array $session;
    public array $files;
    public array $post;
    public array $get;
    public array $server;
    public array $cookie;
    public array $env;
    public ?array $jsonData;

    public function __construct(
        string $method = null,
        string $uri = null,
        array  $get = [],
        array  $post = [],
        array  $session = [],
        array  $files = [],
        array  $server = [],
        array  $cookie = [],
        array  $env = [],
        ?array  $jsonData = null
    )
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->session = $session;
        $this->post = $post;
        $this->get = $get;
        $this->files = $files;
        $this->server = $server;
        $this->cookie = $cookie;
        $this->env = $env;
        $this->jsonData = $jsonData;
    }
}
