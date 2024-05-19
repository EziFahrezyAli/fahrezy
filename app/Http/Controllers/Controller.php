<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function sendResponse($result, $message = 'Success get data', $code = 200)
    {
        $response = [
            'message' => $message,
            'data' => $result
        ];

        return response()->json($response, $code);
    }

    protected function sendError($error, $errorTitle = 'Opps, something was wrong!', $code = 400)
    {
        $response = ['message' => $errorTitle, 'errors' => $error];

        return response()->json($response, $code);
    }
}
