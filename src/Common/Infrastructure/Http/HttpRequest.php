<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Infrastructure\Http;

use InvalidArgumentException;

class HttpRequest
{
    public function __construct(
        public ?string $method = null,
        public ?string $uri = null,
        public array   $get = [],
        public array   $post = [],
        public array   $files = [],
        public array   $server = [],
        public array   $cookie = [],
        public array   $env = [],
        public ?array  $jsonData = null
    )
    {
        if (isset($this->server["CONTENT_TYPE"]) && preg_match("|\Aapplication/json|ui", $this->server["CONTENT_TYPE"])) {
            $this->jsonData = json_decode(file_get_contents('php://input'), true);
            if ($this->jsonData === false) {
                throw new InvalidArgumentException("content-type is application/json. but parse failed");
            }
        }
    }
}
