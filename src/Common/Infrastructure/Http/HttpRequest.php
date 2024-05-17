<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Infrastructure\Http;

use InvalidArgumentException;
use Romira\Zenita\Utils\Collection\Collection;

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
        ?array $jsonData = null
    )
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->session = $session;
        $this->post = Collection::castNumbers($post);
        $this->get = Collection::castNumbers($get);
        $this->files = $files;
        $this->server = $server;
        $this->cookie = $cookie;
        $this->env = $env;

        if (!is_null($jsonData)) {
            $this->jsonData = $jsonData;
        } else if (isset($this->server["CONTENT_TYPE"]) && preg_match("|\Aapplication/json|ui", $this->server["CONTENT_TYPE"])) {
            $this->jsonData = json_decode(file_get_contents('php://input'), true);
            if ($this->jsonData === false) {
                throw new InvalidArgumentException("content-type is application/json. but parse failed");
            }
        }
    }
}
