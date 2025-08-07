<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UserNotFoundException extends Exception
{
    public function __construct(string $message = 'User not found', int $code = 404)
    {
        parent::__construct($message, $code);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => 'User not found',
            'message' => $this->getMessage(),
        ], 404);
    }
}
