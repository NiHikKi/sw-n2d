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

    public function buildTree(): void
    {
        $this->routeTree = [
            Method::GET => [],
            Method::POST => []
        ];

        foreach ($this->routes as $route) {
            foreach ($route->method as $method) {
                $pattern = $this->parsePattern($route->pattern);
                $current = &$this->routeTree[$method];

                foreach ($pattern as $i => $token) {
                    if (str_starts_with($token, '{')) {
                        $current = &$current['{}'];
                    } else {
                        $current = &$current[$token];
                    }
                }
                $current[0] = $route;
            }
        }
    }

    public function match(string $method, string $uri): Route
    {
        $pattern = $this->parsePattern($uri);

        $current = &$this->routeTree[$method];
        foreach ($pattern as $i => $token) {
            if (isset($current[$token])) {
                $current = &$current[$token];
            } elseif (isset($current['{}'])) {
                $current = &$current['{}'];
            }
        }

        return $current[0] ?? throw new NotFoundHttpException();
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