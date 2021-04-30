<?php

declare(strict_types=1);

use Core\Application;
use Swoole\Http\Server;

function PrintLog(mixed $str): void
{
    if (!is_string($str)) {
        $str = \Core\Json\Json::encode($str);
    }
    echo sprintf("[%s] %s" . PHP_EOL, date("Y-m-d H:i:s"), $str);
}

require_once __DIR__ . '/vendor/autoload.php';


$http = new Swoole\Http\Server("*", 8000);

$application = new Application($http);

$http->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use ($application) {
    $application->handle($request, $response);
});


$http->on('start', function (Server $server) {
    PrintLog("Server listening on {$server->host}:{$server->port}");
});

$http->on('shutdown', function (Server $server) {
    PrintLog("Server is shutting down.");
});
$http->start();
