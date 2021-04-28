<?php

declare(strict_types=1);

namespace Core\Router;

use Core\Http\Method;

class Route
{
    /** @var callable $action */
    public $action;
    public ?string $name;
    public string $pattern;
    public array $method;

    /**
     * Route constructor.
     * @param array $method
     * @param string $pattern
     * @param callable $action
     */
    public function __construct(array $method, string $pattern, $action) {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->action = $action;
    }

    /**
     * @param callable $action
     */
    public static function get(string $pattern, $action): self {
        return new self([Method::GET], $pattern, $action);
    }

    /**
     * @param callable $action
     */
    public static function post(string $pattern, $action): self {
        return new self([Method::POST], $pattern, $action);
    }

    public function name(string $name): self {
        $this->name = $name;

        return $this;
    }
}