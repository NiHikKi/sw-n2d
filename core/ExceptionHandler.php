<?php

declare(strict_types=1);

namespace Core;

use Core\Http\Request;
use Core\Http\Response;

class ExceptionHandler
{
    public function __construct()
    {
    }

    public function handle(\Throwable $e, Request $request, Response $response): void
    {
        $this->jsonResponse($response, [
            'type' => $e::class,
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    private function jsonResponse(Response $response, mixed $data): void
    {
        $response->setHeader('Content-type', 'application/json');
        $response->end(json_encode($data));
    }
}
