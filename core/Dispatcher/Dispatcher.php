<?php

declare(strict_types=1);

namespace Core\Dispatcher;

use Core\Container\Container;
use Core\Contract\DispatcherContract;
use Core\Contract\RouterContract;
use Core\Http\Request;
use Core\Http\Response;
use FastRoute\Dispatcher as RouteDispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class Dispatcher implements DispatcherContract
{
    private Container $container;
    private RouterContract $router;

    public function __construct(Container $container, RouterContract $router)
    {
        $this->container = $container;
        $this->router = $router;
    }

    public function dispatch(Request $request, Response $response): void
    {
        $matchRoute = $this->router->match($request);
        $handler = $matchRoute[0];
        $vars = $matchRoute[1];

        PrintLog($handler);
        PrintLog($vars);

        $handlerInstance = $this->container->get($handler[0]);
        $response->end(call_user_func([$handlerInstance, $handler[1]], ...$vars));
    }
}