<?php

use App\Http\Controllers\Api\ComponentsController;
use App\Http\Controllers\Api\LandingsController;
use App\Http\Controllers\Api\UserController;
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


// Components Api routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('components', [ComponentsController::class, 'index']);
    Route::post('components', [ComponentsController::class, 'store']);
    Route::get('components/{component}', [ComponentsController::class, 'show']);
    Route::put('components/{component}', [ComponentsController::class, 'update']);
    Route::delete('components/{component}', [ComponentsController::class, 'destroy']);
});

// Landings Api routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('landings', [landingsController::class, 'index']);
    Route::post('landings', [landingsController::class, 'store']);
    Route::get('landings/{landing}', [landingsController::class, 'show']);
    Route::put('landings/{landing}', [landingsController::class, 'update']);
    Route::get('landings/user/{user_id}', [landingsController::class, 'showUserLanding']);
    Route::delete('landings/{landing}', [landingsController::class, 'destroy']);
    Route::post('/landings/upload-media', [landingsController::class, 'uploadMedia']);
});


Route::group(['middleware' => ['api']], function () {
    Route::prefix('v1')->group(function () {
        Route::post('auth/register', [UserController::class, 'createUser']);
        Route::post('auth/login', [UserController::class, 'loginUser']);
    });

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('auth/logout', [UserController::class, 'logout']);
    Route::get('auth/validateToken', [UserController::class, 'validateToken']);
});
});
