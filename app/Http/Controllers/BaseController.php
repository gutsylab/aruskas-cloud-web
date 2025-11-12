<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{

    protected function response(string $view, array $data = [], string $message = "Success", int $status = 200)
    {

        if (request()->expectsJson()) {
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

        return view($view, $data);
    }
}
