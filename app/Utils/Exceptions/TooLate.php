<?php

namespace App\Utils\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class TooLate extends Exception
{
    public function render(): Response
    {
        $message = $this->getMessage() ?: "از بازه زمانی انجام این عملیات گذشته است.";

        return response()->json([
            'status' => 'error',
            'code' => Response::HTTP_FORBIDDEN,
            'message' => $message,
            'reason' => 'PermissionDenied',
        ], Response::HTTP_FORBIDDEN);
    }
}
