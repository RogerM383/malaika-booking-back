<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PassportController;
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

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::prefix('clients')->group(function () {
    Route::get('/', [ClientController::class, 'get'])->middleware('auth:api');
    Route::get('/{id}', [ClientController::class, 'getById'])->middleware('auth:api');
    Route::post('/', [ClientController::class, 'create'])->middleware('auth:api');
    Route::post('/{id}', [ClientController::class, 'update'])->middleware(['must.json', 'auth:api']);
});

Route::prefix('passports')->group(function () {
    //Route::get('/', [PassportController::class, 'get'])->middleware('auth:api');
    //Route::get('/{id}', [PassportController::class, 'getById'])->middleware('auth:api');
    Route::post('/', [PassportController::class, 'create'])->middleware('auth:api');
    Route::post('/{id}', [PassportController::class, 'update'])->middleware(['must.json', 'auth:api']);
});
