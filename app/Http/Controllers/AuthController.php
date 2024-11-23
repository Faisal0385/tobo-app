<?php

namespace App\Http\Controllers;
use App\Models\Client;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            return jsonResponse("error", 'Invalid credentials', 401, ); ## 401 Unauthorized
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
}
