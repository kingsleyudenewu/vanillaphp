<?php


namespace Kingsley\Vanillaphp;

class Request
{
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return json_encode($response);
    }

    public function sendErrorResponse($message)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];
        return json_encode($response);
    }
}

$request = new Request();