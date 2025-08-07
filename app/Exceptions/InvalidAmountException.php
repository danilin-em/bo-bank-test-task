<?php

namespace App\Exceptions;

use Exception;

class InvalidAmountException extends Exception
{
    public function __construct(string $message = 'Invalid amount', int $code = 400)
    {
        parent::__construct($message, $code);
    }

    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'error' => 'Invalid amount',
            'message' => $this->getMessage(),
        ], 400);
    }
}
