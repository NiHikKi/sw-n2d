<?php

declare(strict_types=1);

namespace Core\Contract;

use Core\Http\Exception\MethodNotAllowedHttpException;
use Core\Http\Exception\NotFoundHttpException;
use Core\Http\Request;

interface RouterContract
{
    /**
     * @throws NotFoundHttpException|MethodNotAllowedHttpException
     */
    public function match(Request $request): array;
}