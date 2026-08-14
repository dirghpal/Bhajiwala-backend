<?php

use Illuminate\Validation\ValidationException;
use App\Http\Exceptions\ApiStatusException;

if (!function_exists('handleApiRequest')) {

    function handleApiRequest($callback)
    {
        try {

            return $callback();

        } catch (ValidationException $e) {

            return response()->json([
                'status' => 0,
                'msg' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);

        } catch (ApiStatusException $e) {

            return response()->json([
                'status' => 0,
                'msg' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 0,
                'msg' => 'Server Error',
            ], 500);
        }
    }
}