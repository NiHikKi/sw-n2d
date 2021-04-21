<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$http = new Swoole\Http\Server("127.0.0.1", 8000);

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/users', [\Ports\Http\Controller\IndexController::class, 'index']);
    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

$http->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($dispatcher) {
    // Fetch method and URI from somewhere
    $httpMethod = $request->server['request_method'];;
    $uri = $request->server['request_uri'];

    // Strip query string (?foo=bar) and decode URI
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }
    $uri = rawurldecode($uri);

    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            $response->status(404, 'Not Found');
            // ... 404 Not Found
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];

            $response->status(405, 'Method Not Allowed');
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            // ... call $handler with $vars
            $response->end($handler($vars));
            break;
    }
});

$http->start();