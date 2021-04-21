<?php

declare(strict_types=1);

namespace Ports\Http\Controller;

class IndexController
{
    public static function index(): string
    {
        return 'test';
    }
}