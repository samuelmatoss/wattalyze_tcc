<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;

class InvalidDataException extends Exception
{
    protected $errors;
    protected $inputData;

    public function __construct($errors, $inputData = [], $message = "Invalid data provided", $code = 422)
    {
        $this->errors = $errors instanceof MessageBag ? $errors->all() : $errors;
        $this->inputData = $inputData;
        parent::__construct($message, $code);
    }

    public function report()
    {
        Log::warning("Invalid data submitted", [
            'errors' => $this->errors,
            'input' => $this->inputData,
            'user_id' => auth()->id(),
        ]);
    }

    public function render($request)
    {
        return new JsonResponse([
            'error' => $this->getMessage(),
            'errors' => $this->errors,
            'suggestions' => [
                'Check the data format requirements',
                'Verify measurement units',
                'Ensure timestamps are in ISO 8601 format'
            ],
            'documentation_url' => route('api.docs.energy-data')
        ], 422);
    }
}