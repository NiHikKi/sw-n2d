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

    /** @psalm-suppress MixedAssignment,MixedArgument,MixedMethodCall */
    public function dispatch(Request $request, Response $response): void
    {
        $matchRoute = $this->router->match($request);
        /** @var array{0: class-string, 1: string} $handler */
        $handler = $matchRoute[0];
        /** @var array $vars */
        $vars = $matchRoute[1];

        $handlerInstance = $this->container->get($handler[0]);
        $response->end($handlerInstance->{$handler[1]}(...$vars));
    }
}