<?php

namespace App\Exceptions;

use Exception;

class InsufficientFundsException extends Exception
{
    public function __construct(string $message = 'Insufficient funds', int $code = 400)
    {
        parent::__construct($message, $code);
    }

    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'error' => 'Insufficient funds',
            'message' => $this->getMessage(),
        ], 400);
    }
}
