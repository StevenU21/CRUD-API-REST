<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class NotFoundException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => 'Resource does not exist',
        ], 404);
    }
}
