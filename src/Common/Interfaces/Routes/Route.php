<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Interfaces\Routes;

use Exception;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Utils\Collection\Collection;

class Route
{
    private array $routes = [];
    private HttpRequest $httpRequest;

    public function __construct(HttpRequest $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    /**
     * @param string $route
     * @param HandlerInterface $handler
     * @return $this
     */
    public function get(string $route, HandlerInterface $handler): Route
    {
        $this->routes['GET'][$route] = $handler;

        return $this;
    }

    /**
     * @param string $route
     * @param HandlerInterface $handler
     * @return $this
     */
    public function post(string $route, HandlerInterface $handler): Route
    {
        $this->routes['POST'][$route] = $handler;

        return $this;
    }

    public function run(): void
    {
        $request_method = $this->httpRequest->method;
        $request_uri = $this->httpRequest->uri;

        /**
         * @var string $route
         * @var HandlerInterface $handler
         */
        foreach ($this->routes[$request_method] as $route => $handler) {
            $pattern = $this->createPattern($route);

            if (preg_match($pattern, $request_uri, $matches)) {
                $matches = Collection::castNumbers($matches);

                try {
                    $response = $handler->handle($this->httpRequest, $matches);
                } catch (Exception $e) {
                    $response = new HttpResponse(statusCode: 500, body: 'Internal Server Error. ' . $e);
                }
                $response->emit();
                return;
            }
        }

        (new HttpResponse(statusCode: 404, body: 'Not Found'))->emit();
    }

    /**
     * ルーターに登録されたルートを正規表現パターンに変換する
     * Example: /posts/{post_id} -> /\A/posts\/(?P<post_id>[a-zA-Z0-9_-]+)\z/u
     *
     * @param string $route
     * @return string
     */
    private function createPattern(string $route): string
    {
        $pattern = preg_quote($route, '/');
        $pattern = preg_replace_callback('/\\\{(\w+)\\\}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[a-zA-Z0-9_-]+)';
        }, $pattern);

        return '/\A' . $pattern . '\z/u';
    }
}
