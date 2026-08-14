<?php

namespace App\Helpers;

class ControllerHelper
{
    /**
     * Success Response
     */
    public static function success($data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Error Response
     */
    public static function error(string $message = 'Something went wrong', int $code = 500, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Validation Error
     */
    public static function validation($errors)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation Error',
            'errors' => $errors,
        ], 422);
    }

    /**
     * Not Found
     */
    public static function notFound(string $message = 'Record not found')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }

    /**
     * Unauthorized
     */
    public static function unauthorized(string $message = 'Unauthorized')
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }
}