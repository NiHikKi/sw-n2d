<?php

declare(strict_types=1);

namespace Core\Router;

use Core\Http\Exception\NotFoundHttpException;
use Core\Http\Method;

class RouteCollection implements \JsonSerializable
{
    /** @var Route[] $routes  */
    private array $routes;

    private array $routeTree;

    public function __construct()
    {
        $this->routes = [];
        $this->routeTree = [];
    }

    public function add(Route $route): self
    {
        /// TODO: validation
        $this->routes[] = $route;

        return $this;
    }

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-suppress InvalidArrayOffset
     * @psalm-suppress MixedArrayAssignment
     */
    public function buildTree(): void
    {
        $this->routeTree = [
            Method::GET => [],
            Method::POST => []
        ];

        foreach ($this->routes as $route) {
            /** @var string $method */
            foreach ($route->method as $method) {
                $pattern = $this->parsePattern($route->pattern);
                $current = &$this->routeTree[$method];

                /** @var string $token */
                foreach ($pattern as $token) {
                    if (str_starts_with($token, '{')) {
                        $current = &$current['{}'];
                        $paramToken = explode(':', trim($token, '{}'));
                        $current[1] = $paramToken[0];
                        $current[2] = $paramToken[1] ?? '*';
                    } else {
                        $current = &$current[$token];
                    }
                }
                $current[0] = $route;
            }
        }
    }

    /**
     * @param string $method
     * @param string $uri
     * @return array{0: Route, 1: array}
     * @throws NotFoundHttpException
     *
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayOffset
     */
    public function match(string $method, string $uri): array
    {
        $pattern = $this->parsePattern($uri);
        $params = [];

        $current = &$this->routeTree[$method];
        foreach ($pattern as $token) {
            if (isset($current[$token])) {
                $current = &$current[$token];
            } elseif (isset($current['{}'])) {
                $current = &$current['{}'];
                $params[$current[1]] = $token;
            }
        }

        if (isset($current[0])) {
            /** @var array{0: Route} $current */
            return [$current[0], $params];
        }

        throw new NotFoundHttpException();
    }

    private function parsePattern(string $pattern): array
    {
        $pattern = trim($pattern, '/');
        $tokens = explode("/", $pattern);

        return $tokens;
    }

    public function jsonSerialize()
    {
        return [
            'routes' => $this->routes,
            'routeTree' => $this->routeTree
        ];
    }
}