<?php

namespace App\Exceptions;

use App\Services\ApiService;
use Exception;

class GeneralApiException extends Exception
{
    private ApiService $apiService;

    public function render($request) {
        if (is_null($this->getCode())) {
            $this->code = 500;
        }
        return (new ApiService())->getErrorResponse($this->getMessage() ?? '', $this->getCode());
    }
}
