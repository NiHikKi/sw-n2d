<?php

declare(strict_types=1);

namespace Core\Http;

class Request
{
    private ?\Swoole\Http\Request $request = null;

    public static function fromSwooleRequest(\Swoole\Http\Request $request): self
    {
        $self = new self();
        $self->setSwooleRequest($request);

        return $self;
    }

    private function setSwooleRequest(\Swoole\Http\Request $request): void
    {
        $this->request = $request;
    }

    public function server(string $key): ?string
    {
        return $this->request->server[$key] ?? null;
    }
}