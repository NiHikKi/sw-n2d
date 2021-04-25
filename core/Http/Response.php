<?php

declare(strict_types=1);

namespace Core\Http;

class Response
{

    private ?\Swoole\Http\Response $response;

    public static function fromSwooleResponse(\Swoole\Http\Response $response): self
    {
        $self = new self();

        $self->setSwooleResponse($response);

        return $self;
    }

    public function status(int $code, string $data): void
    {
        $this->response?->status($code, $data);
    }

    public function end(string $data): void
    {
        $this->response?->end($data);
    }

    private function setSwooleResponse(\Swoole\Http\Response $response): void
    {
        $this->response = $response;
    }

    public function setHeader(string $name, string $value): void
    {
        $this->response->setHeader($name, $value);
    }
}