<?php

namespace App\Utils\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CustomException extends Exception
{
    public function render(): Response
    {
        $message = "مشکلی پیش آمد.";
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        $status = 'error';
        $reason = "Unknown";


        switch ($this->getMessage()) {
            case "Access Denied":
                $message = "دسترسی مجاز نیست.";
                $code = Response::HTTP_FORBIDDEN;
                $reason = "PermissionDenied";
                break;
            case "Already Paid":
                $message = "هزینه پرداخت شده است.";
                $code = Response::HTTP_FORBIDDEN;
                $reason = "AlreadyPaid";
                break;
            case "Impossible Request":
                $message = "انجام این عملیات ممکن نیست.";
                $code = Response::HTTP_METHOD_NOT_ALLOWED;
                $reason = "ImpossibleRequest";
                break;
            case "Insufficient Stock":
                $message = "تعداد کافی از کالای مورد نظر موجود نیست.";
                $code = Response::HTTP_BAD_REQUEST;
                $reason = "InsufficientStock";
                break;
            case "Invalid Credentials":
                $message = "اطلاعات وارد شده درست نیست.";
                $code = Response::HTTP_UNPROCESSABLE_ENTITY;
                $reason = "InvalidCredentials";
                break;
            case "Invalid Status":
                $message = "وضعیت وارد شده درست نیست.";
                $code = Response::HTTP_UNPROCESSABLE_ENTITY;
                $reason = "InvalidStatus";
                break;
            case "Non-Existent Address":
                $message = "آدرس مورد نظر یافت نشد.";
                $code = Response::HTTP_NOT_FOUND;
                $reason = "NonExistentAddress";
                break;
            case "Non-Existent Item":
                $message = "آیتم مورد نظر یافت نشد.";
                $code = Response::HTTP_NOT_FOUND;
                $reason = "NonExistentItem";
                break;
            case "Non-Existent Order":
                $message = "سفارش مورد نظر یافت نشد.";
                $code = Response::HTTP_NOT_FOUND;
                $reason = "NonExistentOrder";
                break;
            case "Too Late":
                $message = "از بازه زمانی انجام این عملیات گذشته است.";
                $code = Response::HTTP_FORBIDDEN;
                $reason = "TooLate";
                break;
            case "YOU ARE POOR":
                $message = "موجودی شما کافی نیست.";
                $code = Response::HTTP_BAD_REQUEST;
                $reason = "YouArePoor";
                break;
        }

        return response()->json([
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'reason' => $reason,
        ], $code);
    }
}
