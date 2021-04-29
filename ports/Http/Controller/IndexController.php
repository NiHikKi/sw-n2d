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

    public function view(int $id): string
    {
        return (string)$id;
    }

    public function testVars(int $id, string $name, string $val): string
    {
        return "$id - $name - $val";
    }
}