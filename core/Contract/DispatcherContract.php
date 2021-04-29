<?php

declare(strict_types=1);

namespace Core\Contract;

use Core\Http\Request;
use Core\Http\Response;

interface DispatcherContract
{
    public function dispatch(Request $request, Response $response): void;
}
