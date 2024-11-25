<?php

namespace App\Http\Controllers;
use App\Jobs\ResetMail;
use App\Jobs\SendMail;
use App\Mail\ResetPasswordMail;
use App\Models\Client;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mail;
use Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        ## Validate the input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return jsonResponse("error", 'Validation failed', 422, $validator->errors()); ## 422 Unprocessable Entity
        }

        ## Retrieve client by email
        $client = Client::where('email', $request->email)->first();

        if ($client && Hash::check($request->password, $client->password)) {
            ## Prepare the payload with the client data
            $payload = [
                'sub' => $client->id,   ## Subject (client ID)
                'email' => $client->email,
                'role' => $client->role,
            ];

            ## Generate JWT token
            $token = generateJWTToken($payload);

            return jsonLoginSuccess($token, $client);
        } else {
            return jsonResponse("error", 'Invalid credentials', 401); ## 401 Unauthorized
        }
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email|max:255',
            'password' => 'required|string|min:5|max:255',
            'phone' => 'required|string|max:15',
            'image' => 'nullable|string|max:255',
        ]);

        try {
            $client = new Client();
            $client->name = $request->name;
            $client->email = $request->email;
            $client->password = Hash::make($request->password);
            $client->phone = $request->phone;
            $client->image = $request->image;
            $client->created_at = Carbon::now();
            $client->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Registered Successfully!',
                'data' => null,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration Failed!',
                'data' => null,
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $email = $request->email;
        $data = Client::where('email', '=', $email)->first();

        if (empty($data)) {
            return jsonResponse("error", 'No data found!!', 404);
        }

        try {
            $payload = [
                'email' => $email,
            ];

            ## Generate JWT token
            $token = generatePassJWTToken($payload);

            $otpNum = rand(1000, 9999);

            $data->update([
                'otp' => $otpNum
            ]);

            ResetMail::dispatch($otpNum, $email);
            return jsonResponse("success", 'Pls check your email!!', 200, null, $token);
        } catch (\Throwable $th) {
            return jsonResponse("error", 'Invalid credentials', 401);
        }
    }

    public function otp(Request $request)
    {
        ## Get the token from the Authorization header (Bearer token)
        $token = $request->bearerToken();

        if (!$token) {
            return jsonResponse("error", 'Token not provided', 401); ## 401 Unauthorized
        }

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), env('JWT_ALGO', 'HS256')));
            $data = Client::where('email', '=', $decoded->email)->where('otp', '=', $request->otp)->first();

            if (empty($data)) {
                return jsonResponse("error", 'Invalid credentials', 401);
            }

            return jsonResponse("success", 'OTP is valid', 200);
        } catch (\Throwable $th) {
            return jsonResponse("error", 'Invalid credentials', 401);
        }
    }

    public function newPassword(Request $request)
    {
        ## Validate the input
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|confirmed|min:5|max:255',
        ]);

        if ($validator->fails()) {
            return jsonResponse("error", 'Validation failed', 422, $validator->errors()); ## 422 Unprocessable Entity
        }

        ## Get the token from the Authorization header (Bearer token)
        $token = $request->bearerToken();

        if (!$token) {
            return jsonResponse("error", 'Token not provided', 401); ## 401 Unauthorized
        }

        try {

            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), env('JWT_ALGO', 'HS256')));

            $data = Client::where('email', '=', $decoded->email)->whereNotNull('otp')->first();

            if (empty($data)) {
                return jsonResponse("error", 'Invalid credentials', 401);
            }

            $data->update([
                'otp' => null,
                'password' => Hash::make($request->password)
            ]);

            return jsonResponse("success", 'Password updated successfully!!', 200);
        } catch (\Throwable $th) {
            return jsonResponse("error", 'Invalid credentials', 401);
        }
    }

    public function verifyEmail($email)
    {
        ## Validate the input
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return jsonResponse("error", 'Validation failed', 422, $validator->errors()); ## 422 Unprocessable Entity
        }

        $client = Client::where('email', '=', $email)->first();

        if ($client) {
            switch ($client->verify) {
                case false:
                    $client->update([
                        'verify' => true
                    ]);
                    return redirect('/login');
                case true:
                    return jsonResponse("error", 'Already verified!!', 200);
                default:
                    return redirect('/login');
            }
        } else {
            return jsonResponse("error", 'No email found!!', 404); ## 422 Unprocessable Entity
        }
    }
}
