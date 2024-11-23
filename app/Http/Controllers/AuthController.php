<?php

namespace App\Http\Controllers;
use App\Models\Client;
use Carbon\Carbon;
use DB;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return $request;
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
