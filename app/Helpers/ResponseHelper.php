<?php

use Carbon\Carbon;
use Firebase\JWT\JWT;

if (!function_exists('jsonResponse')) {
    /**
     * Helper function to generate JSON responses.
     *
     * @param string $status
     * @param string $message
     * @param int $statusCode
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    function jsonResponse($status, $message, $statusCode = 200, $error = null, $data = null)
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if ($error) {
            $response['error'] = $error;
        }

        if ($data) {
            $response['data'] = $data;
        }

        return response()->json(data: $response, status: $statusCode);
    }
}

if (!function_exists('jsonLoginSuccess')) {
    /**
     * Helper function to generate a JSON response for successful login.
     *
     * @param string $token
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    function jsonLoginSuccess($token, $data)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'data' => $data
        ], 200); // 200 OK
    }
}


if (!function_exists('generateJWTToken')) {
    /**
     * Helper function to generate JWT token.
     *
     * @param array $payload
     * @return string
     */
    function generateJWTToken($payload)
    {
        // Set timezone (optional)
        // date_default_timezone_set("Asia/Dhaka");

        $secretKey = env('JWT_SECRET');
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600 * 8; // 8 hours validity
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expirationTime;

        return JWT::encode($payload, $secretKey, env('JWT_ALGO', 'HS256'));
    }
}