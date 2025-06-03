<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonController
{
    public function success(mixed $data = [], int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }

    public function error(string $message, int $status = 400): JsonResponse
    {
        return response()->json(['message' => $message], $status);
    }
}
