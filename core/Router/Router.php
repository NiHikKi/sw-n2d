<?php

declare(strict_types=1);

namespace Core\Router;

use Core\Contract\RouterContract;
use Core\Http\Exception\MethodNotAllowedHttpException;
use Core\Http\Exception\NotFoundHttpException;
use Core\Http\Request;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher as RouteDispatcher;
use function FastRoute\simpleDispatcher;

class Router implements RouterContract
{
    private Dispatcher $dispatcher;

    public function __construct() {
        /**
         * @psalm-suppress MissingFile
         * @var callable $routeCollectorFn
         */
        $routeCollectorFn = include __DIR__.'/../../routes/api.php';
        $this->dispatcher = simpleDispatcher($routeCollectorFn);
    }


    public function match(Request $request)
    {
        /** @var string $httpMethod */
        $httpMethod = $request->server('request_method');
        /** @var string $uri */
        $uri = $request->server('request_uri');

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case RouteDispatcher::NOT_FOUND:
                throw new NotFoundHttpException();
            case RouteDispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                throw new MethodNotAllowedHttpException($allowedMethods);
            case RouteDispatcher::FOUND:
                /** @var array{0: class-string, 1: string} $handler */
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                return [$handler, $vars];
        }
    }
}