<?php

declare(strict_types=1);

namespace Core\Http\Exception;

use Throwable;

class MethodNotAllowedHttpException extends \Exception
{
    private array $allowedMethods;

    public function __construct(array $allowedMethods, string $message = "", int $code = 0, Throwable $previous = null)
    {
        $this->allowedMethods = $allowedMethods;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
