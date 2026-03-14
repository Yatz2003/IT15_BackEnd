<?php

namespace App\Exceptions;

use RuntimeException;

class WeatherProxyException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $statusCode,
        private readonly ?string $errorCode = null,
    ) {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function errorCode(): ?string
    {
        return $this->errorCode;
    }
}
