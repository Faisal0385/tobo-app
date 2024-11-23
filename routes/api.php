<?php

use App\Http\Controllers\AuthController;
use App\Jobs\SendMail;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('v1/auth/login', [AuthController::class, 'login']);
Route::post('v1/auth/register', [AuthController::class, 'register']);

Route::middleware(['jwt.auth'])->group(function () {
    Route::get('v1/clients', function () {
        $data = Client::get();
        return response()->json($data);
    });
});



Route::get('v1/send-email', function () {
    SendMail::dispatch();
    dd("email send");
});
