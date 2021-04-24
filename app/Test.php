<?php

declare(strict_types=1);

namespace Application;

class Test
{
    private DependencyTest $dependency;

    public function __construct(DependencyTest $dependency) {
        $this->dependency = $dependency;
    }

    public function getString(): string {
        return $this->dependency->log();
    }
}