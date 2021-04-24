<?php

namespace Swoole\Http {
    class Request {
        /**
         * @var array{request_uri:string,request_method:string} $server
         */
        public $server;
    }

    class Response {
        public function status(int $code, string $text): void
        {}

        public function end(mixed $data): void
        {}
    }
}