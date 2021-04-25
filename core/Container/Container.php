<?php

declare(strict_types=1);

namespace Core\Container;

use Core\Contract\DispatcherContract;
use Core\Dispatcher\Dispatcher;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /** @var callable[]  */
    protected array $bindings = [
        DispatcherContract::class => Dispatcher::class
    ];

    protected array $resolved = [];

    /**
     * @param class-string $id
     * @return mixed
     */
    public function get($id)
    {
        if ($id === static::class) {
            return $this;
        }

        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        if (isset($this->bindings[$id])) {
            if (class_exists($this->bindings[$id])) {
                return $this->newInstance($this->bindings[$id]);
            }
            return $this->bindings[$id]($this);
        }
        $instance = $this->newInstance($id);

        return $instance;
    }

    public function newInstance(string $id): mixed
    {
        PrintLog('instance '.$id);
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
    public function has($id)
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