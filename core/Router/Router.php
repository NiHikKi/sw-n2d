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
    private RouteCollection $routes;

    public function __construct() {
        /**
         * @psalm-suppress MissingFile
         * @var callable $routeCollectorFn
         */
        $routeCollectorFn = include __DIR__.'/../../routes/api.php';
        $this->routes = new RouteCollection();
        $routeCollectorFn($this->routes);
        $this->routes->buildTree();
    }

    /**
     * @param Request $request
     * @return array{0: array{0: class-string, 1: string}|callable, 1: array}
     * @throws NotFoundHttpException
     */
    public function match(Request $request): array
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

        [$route, $params] = $this->routes->match($httpMethod, $uri);

        return [$route->action, $params];
    }
}