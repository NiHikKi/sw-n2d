<?php

declare(strict_types=1);

namespace Core\Container;

use Core\Contract\DispatcherContract;
use Core\Contract\RouterContract;
use Core\Dispatcher\Dispatcher;
use Core\Router\Router;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /** @var array<array-key, class-string|callable>  */
    protected array $bindings = [
        DispatcherContract::class => Dispatcher::class,
        RouterContract::class => Router::class,
    ];

    protected array $resolved = [];

    /**
     * @template T
     * @psalm-suppress MoreSpecificImplementedParamType
     * @param class-string<T> $id
     * @return mixed
     * @psalm-return T
     */
    public function get(string $id)
    {
        if ($id === self::class) {
            /** @var T */
            return $this;
        }

        if (isset($this->resolved[$id])) {
            /** @var T */
            return $this->resolved[$id];
        }

        if (isset($this->bindings[$id])) {
            if (is_string($this->bindings[$id]) && class_exists($this->bindings[$id])) {
                /** @var T */
                return $this->newInstance($this->bindings[$id]);
            }

            /** @var T */
            return $this->bindings[$id]($this);
        }

        /** @var T */
        return $this->newInstance($id);
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     * @throws \ReflectionException
     */
    public function newInstance(string $id): mixed
    {
        $reflectionClass = new \ReflectionClass($id);

        $dependencies = $this->buildDependencies($reflectionClass);

        $instance = $reflectionClass->newInstanceArgs($dependencies);

        $this->resolved[$id] = $instance;

        return $instance;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return class_exists($id) || isset($this->bindings[$id]);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return array<int, mixed>
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