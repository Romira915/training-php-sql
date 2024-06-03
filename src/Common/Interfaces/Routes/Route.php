<?php

declare(strict_types=1);

namespace Romira\Zenita\Common\Interfaces\Routes;

use Exception;
use Romira\Zenita\Common\Infrastructure\Http\HttpRequest;
use Romira\Zenita\Common\Infrastructure\Http\HttpResponse;
use Romira\Zenita\Common\Interfaces\Handlers\HandlerInterface;
use Romira\Zenita\Common\Interfaces\Handlers\SessionHandlerInterface;
use Romira\Zenita\Common\Interfaces\Session\Session;
use Romira\Zenita\Common\Interfaces\Session\SessionHandler;

class Route
{
    private array $routes = [];
    private HttpRequest $httpRequest;
    private SessionHandler $sessionHandler;

    public function __construct(HttpRequest $httpRequest, SessionHandler $sessionHandler = new SessionHandler())
    {
        $this->httpRequest = $httpRequest;
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @param string $route
     * @param HandlerInterface|SessionHandlerInterface $handler
     * @return $this
     */
    public function get(string $route, HandlerInterface|SessionHandlerInterface $handler): Route
    {
        $this->routes['GET'][$route] = $handler;

        return $this;
    }

    /**
     * @param string $route
     * @param HandlerInterface|SessionHandlerInterface $handler
     * @return $this
     */
    public function post(string $route, HandlerInterface|SessionHandlerInterface $handler): Route
    {
        $this->routes['POST'][$route] = $handler;

        return $this;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $request_method = $this->httpRequest->method;
        $request_uri = parse_url($this->httpRequest->uri, PHP_URL_PATH);

        /**
         * @var string $route
         * @var HandlerInterface $handler
         */
        foreach ($this->routes[$request_method] as $route => $handler) {
            $pattern = $this->createPattern($route);

            if (preg_match($pattern, $request_uri, $matches)) {
                $response = null;
                if ($handler instanceof HandlerInterface) {
                    $response = $handler::handle($this->httpRequest, $matches);
                } else if ($handler instanceof SessionHandlerInterface) {
                    $this->sessionHandler::start();
                    $session = new Session($this->sessionHandler::getAll());
                    $response = $handler::handle($this->httpRequest, $matches, $session);
                    $this->sessionHandler::setAll($session->all());
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
    public function createPattern(string $route): string
    {
        $pattern = preg_quote($route, '/');
        $pattern = preg_replace_callback('/\\\{(\w+)\\\}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[a-zA-Z0-9_-]+)';
        }, $pattern);

        return '/\A' . $pattern . '\z/u';
    }
}
