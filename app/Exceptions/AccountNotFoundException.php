<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class AccountNotFoundException extends Exception
{
    public function __construct(string $message = 'Account not found', int $code = 404)
    {
        parent::__construct($message, $code);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => 'Account not found',
            'message' => $this->getMessage(),
        ], 404);
    }
}
