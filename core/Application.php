<?php

declare(strict_types=1);

namespace Core;

use Core\Container\Container;
use Core\Dispatcher\Dispatcher;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Application
{
    private Container $container;
    private Dispatcher $dispatcher;
    private ExceptionHandler $exceptionHandler;

    public function __construct() {
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

    private function initExceptionHandler(): void
    {
        $this->exceptionHandler = $this->container->get(ExceptionHandler::class);
    }

    public function handle(Request $request, Response $response)
    {
        try {
            $this->dispatcher->dispatch($request, $response);
        } catch (\Throwable $e) {
            $this->exceptionHandler->handle($e, $request, $response);
        }
    }

}