<?php

namespace Source\Core;

class Response
{
    public function __construct(
        private array $data,
        private int $httpResponseCode = 200
    ) {
        $this->data = $data;
        $this->httpResponseCode = $httpResponseCode;
        return $this;
    }

    /**
     * Return the given data as JSON
     */
    public function json(): string
    {
        http_response_code($this->httpResponseCode);
        return json_encode(["data" => ($this->data ?? '')]);
    }
}
