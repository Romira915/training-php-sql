<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Interfaces\Routes;

use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Utils\Collection\Collection;

class Route
{
    private array $routes = [];

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
        $request_method = $_SERVER['REQUEST_METHOD'];
        $request_uri = $_SERVER['REQUEST_URI'];

        /**
         * @var string $route
         * @var HandlerInterface $handler
         */
        foreach ($this->routes[$request_method] as $route => $handler) {
            $pattern = $this->createPattern($route);

            if (preg_match($pattern, $request_uri, $matches)) {
                $matches = Collection::castNumbers($matches);
                $request = $this->createHttpRequest();

                $response = $handler->handle($request, $matches);
                $response->emit();
                return;
            }
        }

        (new HttpResponse(statusCode: 404, body: 'Not Found'))->emit();
    }

    private function createPattern(string $route): string
    {
        $pattern = preg_quote($route, '/');
        $pattern = preg_replace_callback('/\\\{(\w+)\\\}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[a-zA-Z0-9_-]+)';
        }, $pattern);

        return '/^' . $pattern . '$/u';
    }

    private function createHttpRequest(): HttpRequest
    {
        return new HttpRequest(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            $_GET,
            $_POST,
            $_SESSION ?? [],
            $_FILES,
            $_SERVER,
            $_COOKIE,
            $_ENV
        );
    }
}
