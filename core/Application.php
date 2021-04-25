<?php

declare(strict_types=1);

namespace Core;

use Core\Container\Container;
use Core\Contract\DispatcherContract as Dispatcher;
use Core\Http\Request;
use Core\Http\Response;
use Swoole\Http\Server;

class Application
{
    public static self $instance;

    private Container $container;
    private Dispatcher $dispatcher;
    private ExceptionHandler $exceptionHandler;

    private Server $server;

    public function __construct(Server $server) {

        echo "Creating application".PHP_EOL;

        self::$instance = $this;
        $this->server = $server;

        $this->initContainer();
        $this->initExceptionHandler();
        $this->initDispatcher();
    }

    private function initContainer(): void
    {
        $this->container = new Container();
    }

    private function initDispatcher(): void
    {
        $this->dispatcher = $this->container->get(Dispatcher::class);
    }

    /**
     * @param class-string $id
     */
    public function resolve(string $id): mixed
    {
        return $this->container->get($id);
    }

    private function initExceptionHandler(): void
    {
        $this->exceptionHandler = $this->container->get(ExceptionHandler::class);
    }

    public function handle(\Swoole\Http\Request $request, \Swoole\Http\Response $response): void
    {
        $request = Request::fromSwooleRequest($request);
        $response = Response::fromSwooleResponse($response);
        try {
            $this->dispatcher->dispatch($request, $response);
        } catch (\Throwable $e) {
            $this->exceptionHandler->handle($e, $request, $response);
        }
    }

    /**
     * @return Server
     */
    public function getSwooleServer(): Server
    {
        return $this->server;
    }

}