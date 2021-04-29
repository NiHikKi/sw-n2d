<?php

declare(strict_types=1);

namespace Core\Router;

use Core\Http\Method;

class Route
{
    /** @var array{0: class-string, 1: string}|callable $action */
    public $action;
    public ?string $name = null;
    public string $pattern;
    public array $method;

    /**
     * Route constructor.
     * @param array $method
     * @param string $pattern
     * @param array{0: class-string, 1: string}|callable $action
     */
    public function __construct(array $method, string $pattern, $action)
    {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->action = $action;
    }

    /**
     * @param string $pattern
     * @param array{0: class-string, 1: string}|callable $action
     * @return Route
     */
    public static function get(string $pattern, $action): self
    {
        return new self([Method::GET], $pattern, $action);
    }

    /**
     * @param callable $action
     */
    public static function post(string $pattern, $action): self
    {
        return new self([Method::POST], $pattern, $action);
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
