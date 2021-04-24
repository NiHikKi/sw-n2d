<?php

declare(strict_types=1);

namespace Ports\Http\Controller;

use Application\Test;

class IndexController
{
    private Test $test;

    public function __construct(Test $test) {

        $this->test = $test;
    }

    public function index(): string
    {
        return $this->test->getString();
    }
}