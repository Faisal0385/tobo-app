<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
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
Route::post('v1/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('v1/auth/otp', [AuthController::class, 'otp']);
Route::post('v1/auth/new-password', [AuthController::class, 'newPassword']);

Route::middleware(['jwt.auth'])->group(function () {

    ## Task routes here
    Route::post('v1/add-task', [TaskController::class, 'store']);
    Route::post('v1/update-task/{id}', [TaskController::class, 'update']);
    Route::get('v1/delete-task/{id}', [TaskController::class, 'delete']);

    Route::get('v1/status/{id}/{status}', [TaskController::class, 'status']);

});



