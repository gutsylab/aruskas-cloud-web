<?php

namespace App\Http\Controllers;

class ApiController extends Controller
{
    protected function response(array $data = [], string $message = "Success", int $status = 200)
    {
        $success = $status >= 200 && $status < 300;

        if (!$success) {
            return response()->json([
                'success' => $success,
                'message' => $message,
            ], $status);
        }
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function responseSuccess(array $data = [], string $message = "Success", int $status = 200)
    {
        return response()->json(
            [
                'success' => true,
                'message' => $message,
                "data" => $data,
            ],
            $status
        );
    }

    protected function responseError(string $message = "Error", int $status = 400)
    {
        return response()->json(
            [
                'success' => false,
                'message' => $message,
            ],
            $status
        );
    }
}
