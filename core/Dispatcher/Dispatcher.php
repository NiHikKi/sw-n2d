<?php

declare(strict_types=1);

namespace Core\Dispatcher;

use Core\Container\Container;
use FastRoute\Dispatcher as RouteDispatcher;
use FastRoute\RouteCollector;
use Swoole\Http\Request;
use Swoole\Http\Response;
use function FastRoute\simpleDispatcher;

class Dispatcher
{
    private RouteDispatcher $dispatcher;
    private Container $container;


    public function __construct(Container $container)
    {
        $this->container = $container;
        /**
         * @psalm-suppress MissingFile
         * @var callable $routeCollectorFn
         */
        $routeCollectorFn = include __DIR__.'/../../routes/api.php';
        $this->dispatcher = simpleDispatcher($routeCollectorFn);
        $this->container = $container;
    }

    public function dispatch(Request $request, Response $response): void
    {
        $httpMethod = $request->server['request_method'];
        $uri = $request->server['request_uri'];

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case RouteDispatcher::NOT_FOUND:
                $response->status(404, 'Not Found');
                break;
            case RouteDispatcher::METHOD_NOT_ALLOWED:
                // $allowedMethods = $routeInfo[1];

                $response->status(405, 'Method Not Allowed');
                break;
            case RouteDispatcher::FOUND:
                /** @var array{0: class-string, 1: string} $handler */
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $handlerInstance = $this->container->get($handler[0]);
                $response->end(call_user_func([$handlerInstance, $handler[1]], $vars));
                break;
        }
    }
}