<?php

declare(strict_types=1);

use Core\Application;
use Swoole\Http\Server;

require_once __DIR__ . '/vendor/autoload.php';

$http = new Swoole\Http\Server("127.0.0.1", 8000);

$application = new Application();

$http->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($application) {
    $application->handle($request, $response);
});


$http->on('start', function (Server $server) {
    echo "Server listening on {$server->host}:{$server->port}";
});

$http->on('shutdown', function (Server $server) {
    echo "Server is shutting down.";
});
$http->start();