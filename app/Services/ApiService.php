<?php

namespace App\Services;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;

class ApiService {
    public function getSuccessResponse(string $message, int $status = 200) {
        return response()->json([
            'status' => 'success',
            'message' => $message
        ], $status);
    }

    public function getErrorResponse(string $message = '', int $code = 500) {
        $message = $code === 500 ? 'Whoops, something went wrong.' : $message;
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
}