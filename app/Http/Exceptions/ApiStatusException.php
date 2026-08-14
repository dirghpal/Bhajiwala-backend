<?php

namespace App\Http\Exceptions;

use Exception;

class ApiStatusException extends Exception
{
    public int $statusCode;

    public function __construct(
        string $message,
        int $statusCode = 422
    ) {
        parent::__construct($message);

        $this->statusCode = $statusCode;
    }
}