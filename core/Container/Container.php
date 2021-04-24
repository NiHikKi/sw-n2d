<?php

declare(strict_types=1);

namespace Core\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /** @var callable[]  */
    protected array $bindings = [];

    protected array $resolved = [];

    /**
     * @param class-string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        if ($id === static::class) return $this;

        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        if (isset($this->bindings[$id])) {
            return $this->bindings[$id]($this);
        }

        $reflectionClass = new \ReflectionClass($id);

        $dependencies = $this->buildDependencies($reflectionClass);

        $instance = $reflectionClass->newInstanceArgs($dependencies);

        $this->resolved[$id] = $instance;

        return $instance;
    }

    public function has(string $id): bool
    {
        return class_exists($id) || isset($this->bindings[$id]);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return mixed[]
     */
    private function buildDependencies(\ReflectionClass $reflectionClass): array
    {
        if (!$constructor = $reflectionClass->getConstructor()) {
            return [];
        }

        $params = $constructor->getParameters();

        return array_map(function (\ReflectionParameter $param): mixed {
            if (!$type = (string)$param->getType()) {
                throw new \RuntimeException();
            }
            /** @psalm-var class-string $type */

            return $this->get($type);
        }, $params);
    }
}